<?php

namespace WpStarter\Routing;

use WpStarter\Container\Container;
use WpStarter\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use ReflectionFunction;

class CallableDispatcher implements CallableDispatcherContract
{
    use ResolvesRouteDependencies;

    /**
     * The container instance.
     *
     * @var \WpStarter\Container\Container
     */
    protected $container;

    /**
     * Create a new callable dispatcher instance.
     *
     * @param  \WpStarter\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Dispatch a request to a given callable.
     *
     * @param  \WpStarter\Routing\Route  $route
     * @param  callable  $callable
     * @return mixed
     */
    public function dispatch(Route $route, $callable)
    {
        return $callable(...array_values($this->resolveParameters($route, $callable)));
    }

    /**
     * Resolve the parameters for the callable.
     *
     * @param  \WpStarter\Routing\Route  $route
     * @param  callable  $callable
     * @return array
     */
    protected function resolveParameters(Route $route, $callable)
    {
        return $this->resolveMethodDependencies($route->parametersWithoutNulls(), new ReflectionFunction($callable));
    }
}
