<?php

namespace WpStarter\Database\Eloquent\Casts;

use WpStarter\Contracts\Database\Eloquent\Castable;
use WpStarter\Contracts\Database\Eloquent\CastsAttributes;
use WpStarter\Support\Facades\Crypt;

class AsEncryptedArrayObject implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return \WpStarter\Contracts\Database\Eloquent\CastsAttributes<\WpStarter\Database\Eloquent\Casts\ArrayObject<array-key, mixed>, iterable>
     */
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                if (isset($attributes[$key])) {
                    return new ArrayObject(Json::decode(Crypt::decryptString($attributes[$key])), ArrayObject::ARRAY_AS_PROPS);
                }

                return null;
            }

            public function set($model, $key, $value, $attributes)
            {
                if (! is_null($value)) {
                    return [$key => Crypt::encryptString(Json::encode($value))];
                }

                return null;
            }

            public function serialize($model, string $key, $value, array $attributes)
            {
                return ! is_null($value) ? $value->getArrayCopy() : null;
            }
        };
    }
}
