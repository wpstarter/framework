<?php

namespace WpStarter\Database\Concerns;

use WpStarter\Support\Collection;

trait ExplainsQueries
{
    /**
     * Explains the query.
     *
     * @return \WpStarter\Support\Collection
     */
    public function explain()
    {
        $sql = $this->toSql();

        $bindings = $this->getBindings();

        $explanation = $this->getConnection()->select('EXPLAIN '.$sql, $bindings);

        return new Collection($explanation);
    }
}
