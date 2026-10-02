<?php

namespace WpStarter\Database\Eloquent\Casts;

use WpStarter\Contracts\Database\Eloquent\Castable;
use WpStarter\Contracts\Database\Eloquent\CastsAttributes;
use WpStarter\Support\Stringable;

class AsStringable implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return \WpStarter\Contracts\Database\Eloquent\CastsAttributes<\WpStarter\Support\Stringable, string|\Stringable>
     */
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                return isset($value) ? new Stringable($value) : null;
            }

            public function set($model, $key, $value, $attributes)
            {
                return isset($value) ? (string) $value : null;
            }
        };
    }
}
