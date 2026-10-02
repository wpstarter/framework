<?php

namespace WpStarter\Routing\Contracts;

use WpStarter\Routing\Route;

interface CallableDispatcher
{
    /**
     * Dispatch a request to a given callable.
     *
     * @param  \WpStarter\Routing\Route  $route
     * @param  callable  $callable
     * @return mixed
     */
    public function dispatch(Route $route, $callable);
}
