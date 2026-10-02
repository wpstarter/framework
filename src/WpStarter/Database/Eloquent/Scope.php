<?php

namespace WpStarter\Database\Eloquent;

use WpStarter\Database\Eloquent\Contracts\Model as ModelContract;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @template TModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  \WpStarter\Database\Eloquent\Builder<TModel>  $builder
     * @param  TModel  $model
     * @return void
     */
    public function apply(Builder $builder, ModelContract $model);
}
