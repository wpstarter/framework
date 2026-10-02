<?php

namespace WpStarter\Database\Eloquent;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @template TModel of \WpStarter\Database\Eloquent\Model
     *
     * @param  \WpStarter\Database\Eloquent\Builder<TModel>  $builder
     * @param  TModel  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
