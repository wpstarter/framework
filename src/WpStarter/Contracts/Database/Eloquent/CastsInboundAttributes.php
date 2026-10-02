<?php

namespace WpStarter\Contracts\Database\Eloquent;

use WpStarter\Database\Eloquent\Contracts\Model as ModelContract;


interface CastsInboundAttributes
{
    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \WpStarter\Database\Eloquent\Contracts\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return mixed
     */
    public function set(ModelContract $model, string $key, mixed $value, array $attributes);
}
