<?php

namespace WpStarter\Foundation\Routing;

use WpStarter\Routing\CallableDispatcher;
use WpStarter\Routing\Route;

class PrecognitionCallableDispatcher extends CallableDispatcher
{
    /**
     * Dispatch a request to a given callable.
     *
     * @param  \WpStarter\Routing\Route  $route
     * @param  callable  $callable
     * @return mixed
     */
    public function dispatch(Route $route, $callable)
    {
        $this->resolveParameters($route, $callable);

        ws_abort(204, headers: ['Precognition-Success' => 'true']);
    }
}
