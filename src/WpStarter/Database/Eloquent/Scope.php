<?php

namespace WpStarter\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \WpStarter\Database\Eloquent\Builder  $builder
     * @param  \WpStarter\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
