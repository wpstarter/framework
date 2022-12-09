<?php

namespace WpStarter\Wordpress\Model\Concerns;

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