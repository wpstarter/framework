<?php

namespace WpStarter\Database\Eloquent;

use WpStarter\Database\Events\ModelsPruned;
use LogicException;

trait Prunable
{
    /**
     * Prune all prunable models in the database.
     *
     * @param  int  $chunkSize
     * @return int
     */
    public function pruneAll(int $chunkSize = 1000)
    {
        $total = 0;

        $this->prunable()
            ->when(in_array(SoftDeletes::class, ws_class_uses_recursive(get_class($this))), function ($query) {
                $query->withTrashed();
            })->chunkById($chunkSize, function ($models) use (&$total) {
                $models->each->prune();

                $total += $models->count();

                ws_event(new ModelsPruned(static::class, $total));
            });

        return $total;
    }

    /**
     * Get the prunable model query.
     *
     * @return \WpStarter\Database\Eloquent\Builder
     */
    public function prunable()
    {
        throw new LogicException('Please implement the prunable method on your model.');
    }

    /**
     * Prune the model in the database.
     *
     * @return bool|null
     */
    public function prune()
    {
        $this->pruning();

        return in_array(SoftDeletes::class, ws_class_uses_recursive(get_class($this)))
                ? $this->forceDelete()
                : $this->delete();
    }

    /**
     * Prepare the model for pruning.
     *
     * @return void
     */
    protected function pruning()
    {
        //
    }
}
