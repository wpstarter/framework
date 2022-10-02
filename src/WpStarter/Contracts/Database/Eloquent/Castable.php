<?php

namespace WpStarter\Contracts\Database\Eloquent;

interface Castable
{
    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return string
     * @return string|\WpStarter\Contracts\Database\Eloquent\CastsAttributes|\WpStarter\Contracts\Database\Eloquent\CastsInboundAttributes
     */
    public static function castUsing(array $arguments);
}
