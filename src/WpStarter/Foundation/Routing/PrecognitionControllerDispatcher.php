<?php

namespace WpStarter\Foundation\Routing;

use WpStarter\Routing\ControllerDispatcher;
use WpStarter\Routing\Route;
use RuntimeException;

class PrecognitionControllerDispatcher extends ControllerDispatcher
{
    /**
     * Dispatch a request to a given controller and method.
     *
     * @param  \WpStarter\Routing\Route  $route
     * @param  mixed  $controller
     * @param  string  $method
     * @return void
     */
    public function dispatch(Route $route, $controller, $method)
    {
        $this->ensureMethodExists($controller, $method);

        $this->resolveParameters($route, $controller, $method);

        ws_abort(204, headers: ['Precognition-Success' => 'true']);
    }

    /**
     * Ensure that the given method exists on the controller.
     *
     * @param  object  $controller
     * @param  string  $method
     * @return $this
     */
    protected function ensureMethodExists($controller, $method)
    {
        if (method_exists($controller, $method)) {
            return $this;
        }

        $class = $controller::class;

        throw new RuntimeException("Attempting to predict the outcome of the [{$class}::{$method}()] method but the method is not defined.");
    }
}
