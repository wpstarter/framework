<?php

namespace WpStarter\Contracts\Database\Eloquent;

use WpStarter\Database\Eloquent\Contracts\Model as ModelContract;


interface ComparesCastableAttributes
{
    /**
     * Determine if the given values are equal.
     *
     * @param  \WpStarter\Database\Eloquent\Contracts\Model  $model
     * @param  string  $key
     * @param  mixed  $firstValue
     * @param  mixed  $secondValue
     * @return bool
     */
    public function compare(ModelContract $model, string $key, mixed $firstValue, mixed $secondValue);
}
