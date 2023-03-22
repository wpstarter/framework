<?php

namespace WpStarter\Database\Eloquent;
use WpStarter\Database\Eloquent\Contracts\Model;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \WpStarter\Database\Eloquent\Builder  $builder
     * @param  \WpStarter\Database\Eloquent\Contracts\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
