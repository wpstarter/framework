<?php

namespace WpStarter\Contracts\Auth;

interface Factory
{
    /**
     * Get a guard instance by name.
     *
     * @param  string|null  $name
     * @return \WpStarter\Contracts\Auth\Guard
     */
    public function guard($name = null);
}
