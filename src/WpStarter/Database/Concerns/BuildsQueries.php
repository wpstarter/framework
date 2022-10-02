<?php

namespace WpStarter\Database\Concerns;

use WpStarter\Container\Container;
use WpStarter\Database\Eloquent\Builder;
use WpStarter\Database\MultipleRecordsFoundException;
use WpStarter\Database\RecordsNotFoundException;
use WpStarter\Pagination\Cursor;
use WpStarter\Pagination\CursorPaginator;
use WpStarter\Pagination\LengthAwarePaginator;
use WpStarter\Pagination\Paginator;
use WpStarter\Support\Collection;
use WpStarter\Support\LazyCollection;
use WpStarter\Support\Traits\Conditionable;
use InvalidArgumentException;
use RuntimeException;

trait BuildsQueries
{
    use Conditionable;

    /**
     * Chunk the results of the query.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @return bool
     */
    public function chunk($count, callable $callback)
    {
        $this->enforceOrderBy();

        $page = 1;

        do {
            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $results = $this->forPage($page, $count)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results, $page) === false) {
                return false;
            }

            unset($results);

            $page++;
        } while ($countResults == $count);

        return true;
    }

    /**
     * Run a map over each item while chunking.
     *
     * @param  callable  $callback
     * @param  int  $count
     * @return \WpStarter\Support\Collection
     */
    public function chunkMap(callable $callback, $count = 1000)
    {
        $collection = Collection::make();

        $this->chunk($count, function ($items) use ($collection, $callback) {
            $items->each(function ($item) use ($collection, $callback) {
                $collection->push($callback($item));
            });
        });

        return $collection;
    }

    /**
     * Execute a callback over each item while chunking.
     *
     * @param  callable  $callback
     * @param  int  $count
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function each(callable $callback, $count = 1000)
    {
        return $this->chunk($count, function ($results) use ($callback) {
            foreach ($results as $key => $value) {
                if ($callback($value, $key) === false) {
                    return false;
                }
            }
        });
    }

    /**
     * Chunk the results of a query by comparing IDs.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return bool
     */
    public function chunkById($count, callable $callback, $column = null, $alias = null)
    {
        $column = $column ?? $this->defaultKeyName();

        $alias = $alias ?? $column;

        $lastId = null;

        $page = 1;

        do {
            $clone = clone $this;

            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $results = $clone->forPageAfterId($count, $lastId, $column)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results, $page) === false) {
                return false;
            }

            $lastId = $results->last()->{$alias};

            if ($lastId === null) {
                throw new RuntimeException("The chunkById operation was aborted because the [{$alias}] column is not present in the query result.");
            }

            unset($results);

            $page++;
        } while ($countResults == $count);

        return true;
    }

    /**
     * Execute a callback over each item while chunking by ID.
     *
     * @param  callable  $callback
     * @param  int  $count
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return bool
     */
    public function eachById(callable $callback, $count = 1000, $column = null, $alias = null)
    {
        return $this->chunkById($count, function ($results, $page) use ($callback, $count) {
            foreach ($results as $key => $value) {
                if ($callback($value, (($page - 1) * $count) + $key) === false) {
                    return false;
                }
            }
        }, $column, $alias);
    }

    /**
     * Query lazily, by chunks of the given size.
     *
     * @param  int  $chunkSize
     * @return \WpStarter\Support\LazyCollection
     *
     * @throws \InvalidArgumentException
     */
    public function lazy($chunkSize = 1000)
    {
        if ($chunkSize < 1) {
            throw new InvalidArgumentException('The chunk size should be at least 1');
        }

        $this->enforceOrderBy();

        return LazyCollection::make(function () use ($chunkSize) {
            $page = 1;

            while (true) {
                $results = $this->forPage($page++, $chunkSize)->get();

                foreach ($results as $result) {
                    yield $result;
                }

                if ($results->count() < $chunkSize) {
                    return;
                }
            }
        });
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs.
     *
     * @param  int  $chunkSize
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return \WpStarter\Support\LazyCollection
     *
     * @throws \InvalidArgumentException
     */
    public function lazyById($chunkSize = 1000, $column = null, $alias = null)
    {
        return $this->orderedLazyById($chunkSize, $column, $alias);
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs in descending order.
     *
     * @param  int  $chunkSize
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return \WpStarter\Support\LazyCollection
     *
     * @throws \InvalidArgumentException
     */
    public function lazyByIdDesc($chunkSize = 1000, $column = null, $alias = null)
    {
        return $this->orderedLazyById($chunkSize, $column, $alias, true);
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs in a given order.
     *
     * @param  int  $chunkSize
     * @param  string|null  $column
     * @param  string|null  $alias
     * @param  bool  $descending
     * @return \WpStarter\Support\LazyCollection
     *
     * @throws \InvalidArgumentException
     */
    protected function orderedLazyById($chunkSize = 1000, $column = null, $alias = null, $descending = false)
    {
        if ($chunkSize < 1) {
            throw new InvalidArgumentException('The chunk size should be at least 1');
        }

        $column = $column ?? $this->defaultKeyName();

        $alias = $alias ?? $column;

        return LazyCollection::make(function () use ($chunkSize, $column, $alias, $descending) {
            $lastId = null;

            while (true) {
                $clone = clone $this;

                if ($descending) {
                    $results = $clone->forPageBeforeId($chunkSize, $lastId, $column)->get();
                } else {
                    $results = $clone->forPageAfterId($chunkSize, $lastId, $column)->get();
                }

                foreach ($results as $result) {
                    yield $result;
                }

                if ($results->count() < $chunkSize) {
                    return;
                }

                $lastId = $results->last()->{$alias};
            }
        });
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array|string  $columns
     * @return \WpStarter\Database\Eloquent\Model|object|static|null
     */
    public function first($columns = ['*'])
    {
        return $this->take(1)->get($columns)->first();
    }

    /**
     * Execute the query and get the first result if it's the sole matching record.
     *
     * @param  array|string  $columns
     * @return \WpStarter\Database\Eloquent\Model|object|static|null
     *
     * @throws \WpStarter\Database\RecordsNotFoundException
     * @throws \WpStarter\Database\MultipleRecordsFoundException
     */
    public function sole($columns = ['*'])
    {
        $result = $this->take(2)->get($columns);

        if ($result->isEmpty()) {
            throw new RecordsNotFoundException;
        }

        if ($result->count() > 1) {
            throw new MultipleRecordsFoundException;
        }

        return $result->first();
    }

    /**
     * Paginate the given query using a cursor paginator.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $cursorName
     * @param  \WpStarter\Pagination\Cursor|string|null  $cursor
     * @return \WpStarter\Contracts\Pagination\CursorPaginator
     */
    protected function paginateUsingCursor($perPage, $columns = ['*'], $cursorName = 'cursor', $cursor = null)
    {
        if (! $cursor instanceof Cursor) {
            $cursor = is_string($cursor)
                ? Cursor::fromEncoded($cursor)
                : CursorPaginator::resolveCurrentCursor($cursorName, $cursor);
        }

        $orders = $this->ensureOrderForCursorPagination(! is_null($cursor) && $cursor->pointsToPreviousItems());

        if (! is_null($cursor)) {
            $addCursorConditions = function (self $builder, $previousColumn, $i) use (&$addCursorConditions, $cursor, $orders) {
                $unionBuilders = isset($builder->unions) ? ws_collect($builder->unions)->pluck('query') : ws_collect();

                if (! is_null($previousColumn)) {
                    $builder->where(
                        $this->getOriginalColumnNameForCursorPagination($this, $previousColumn),
                        '=',
                        $cursor->parameter($previousColumn)
                    );

                    $unionBuilders->each(function ($unionBuilder) use ($previousColumn, $cursor) {
                        $unionBuilder->where(
                            $this->getOriginalColumnNameForCursorPagination($this, $previousColumn),
                            '=',
                            $cursor->parameter($previousColumn)
                        );

                        $this->addBinding($unionBuilder->getRawBindings()['where'], 'union');
                    });
                }

                $builder->where(function (self $builder) use ($addCursorConditions, $cursor, $orders, $i, $unionBuilders) {
                    ['column' => $column, 'direction' => $direction] = $orders[$i];

                    $builder->where(
                        $this->getOriginalColumnNameForCursorPagination($this, $column),
                        $direction === 'asc' ? '>' : '<',
                        $cursor->parameter($column)
                    );

                    if ($i < $orders->count() - 1) {
                        $builder->orWhere(function (self $builder) use ($addCursorConditions, $column, $i) {
                            $addCursorConditions($builder, $column, $i + 1);
                        });
                    }

                    $unionBuilders->each(function ($unionBuilder) use ($column, $direction, $cursor, $i, $orders, $addCursorConditions) {
                        $unionBuilder->where(function ($unionBuilder) use ($column, $direction, $cursor, $i, $orders, $addCursorConditions) {
                            $unionBuilder->where(
                                $this->getOriginalColumnNameForCursorPagination($this, $column),
                                $direction === 'asc' ? '>' : '<',
                                $cursor->parameter($column)
                            );

                            if ($i < $orders->count() - 1) {
                                $unionBuilder->orWhere(function (self $builder) use ($addCursorConditions, $column, $i) {
                                    $addCursorConditions($builder, $column, $i + 1);
                                });
                            }

                            $this->addBinding($unionBuilder->getRawBindings()['where'], 'union');
                        });
                    });
                });
            };

            $addCursorConditions($this, null, 0);
        }

        $this->limit($perPage + 1);

        return $this->cursorPaginator($this->get($columns), $perPage, $cursor, [
            'path' => Paginator::resolveCurrentPath(),
            'cursorName' => $cursorName,
            'parameters' => $orders->pluck('column')->toArray(),
        ]);
    }

    /**
     * Get the original column name of the given column, without any aliasing.
     *
     * @param  \WpStarter\Database\Query\Builder|\WpStarter\Database\Eloquent\Builder  $builder
     * @param  string  $parameter
     * @return string
     */
    protected function getOriginalColumnNameForCursorPagination($builder, string $parameter)
    {
        $columns = $builder instanceof Builder ? $builder->getQuery()->columns : $builder->columns;

        if (! is_null($columns)) {
            foreach ($columns as $column) {
                if (($position = stripos($column, ' as ')) !== false) {
                    $as = substr($column, $position, 4);

                    [$original, $alias] = explode($as, $column);

                    if ($parameter === $alias) {
                        return $original;
                    }
                }
            }
        }

        return $parameter;
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param  \WpStarter\Support\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \WpStarter\Pagination\LengthAwarePaginator
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Create a new simple paginator instance.
     *
     * @param  \WpStarter\Support\Collection  $items
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \WpStarter\Pagination\Paginator
     */
    protected function simplePaginator($items, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(Paginator::class, compact(
            'items', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Create a new cursor paginator instance.
     *
     * @param  \WpStarter\Support\Collection  $items
     * @param  int  $perPage
     * @param  \WpStarter\Pagination\Cursor  $cursor
     * @param  array  $options
     * @return \WpStarter\Pagination\CursorPaginator
     */
    protected function cursorPaginator($items, $perPage, $cursor, $options)
    {
        return Container::getInstance()->makeWith(CursorPaginator::class, compact(
            'items', 'perPage', 'cursor', 'options'
        ));
    }

    /**
     * Pass the query to a given callback.
     *
     * @param  callable  $callback
     * @return $this|mixed
     */
    public function tap($callback)
    {
        return $this->when(true, $callback);
    }
}
