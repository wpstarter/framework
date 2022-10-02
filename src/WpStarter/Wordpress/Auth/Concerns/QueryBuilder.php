<?php

namespace WpStarter\Wordpress\Auth\Concerns;

use WpStarter\Database\Eloquent\Builder;

class QueryBuilder extends Builder
{
    public function setModel($model)
    {
        $this->model = $model;
        $this->query->from($model->getTable());
        return $this;
    }
}