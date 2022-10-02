<?php

namespace WpStarter\Routing;

use WpStarter\Support\Arr;
use WpStarter\Support\Traits\Macroable;

class PendingResourceRegistration
{
    use CreatesRegularExpressionRouteConstraints, Macroable;

    /**
     * The resource registrar.
     *
     * @var \WpStarter\Routing\ResourceRegistrar
     */
    protected $registrar;

    /**
     * The resource name.
     *
     * @var string
     */
    protected $name;

    /**
     * The resource controller.
     *
     * @var string
     */
    protected $controller;

    /**
     * The resource options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The resource's registration status.
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * Create a new pending resource registration instance.
     *
     * @param  \WpStarter\Routing\ResourceRegistrar  $registrar
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return void
     */
    public function __construct(ResourceRegistrar $registrar, $name, $controller, array $options)
    {
        $this->name = $name;
        $this->options = $options;
        $this->registrar = $registrar;
        $this->controller = $controller;
    }

    /**
     * Set the methods the controller should apply to.
     *
     * @param  array|string|dynamic  $methods
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function only($methods)
    {
        $this->options['only'] = is_array($methods) ? $methods : func_get_args();

        return $this;
    }

    /**
     * Set the methods the controller should exclude.
     *
     * @param  array|string|dynamic  $methods
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function except($methods)
    {
        $this->options['except'] = is_array($methods) ? $methods : func_get_args();

        return $this;
    }

    /**
     * Set the route names for controller actions.
     *
     * @param  array|string  $names
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function names($names)
    {
        $this->options['names'] = $names;

        return $this;
    }

    /**
     * Set the route name for a controller action.
     *
     * @param  string  $method
     * @param  string  $name
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function name($method, $name)
    {
        $this->options['names'][$method] = $name;

        return $this;
    }

    /**
     * Override the route parameter names.
     *
     * @param  array|string  $parameters
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function parameters($parameters)
    {
        $this->options['parameters'] = $parameters;

        return $this;
    }

    /**
     * Override a route parameter's name.
     *
     * @param  string  $previous
     * @param  string  $new
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function parameter($previous, $new)
    {
        $this->options['parameters'][$previous] = $new;

        return $this;
    }

    /**
     * Add middleware to the resource routes.
     *
     * @param  mixed  $middleware
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function middleware($middleware)
    {
        $middleware = Arr::wrap($middleware);

        foreach ($middleware as $key => $value) {
            $middleware[$key] = (string) $value;
        }

        $this->options['middleware'] = $middleware;

        return $this;
    }

    /**
     * Specify middleware that should be removed from the resource routes.
     *
     * @param  array|string  $middleware
     * @return $this|array
     */
    public function withoutMiddleware($middleware)
    {
        $this->options['excluded_middleware'] = array_merge(
            (array) ($this->options['excluded_middleware'] ?? []), Arr::wrap($middleware)
        );

        return $this;
    }

    /**
     * Add "where" constraints to the resource routes.
     *
     * @param  mixed  $wheres
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function where($wheres)
    {
        $this->options['wheres'] = $wheres;

        return $this;
    }

    /**
     * Indicate that the resource routes should have "shallow" nesting.
     *
     * @param  bool  $shallow
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function shallow($shallow = true)
    {
        $this->options['shallow'] = $shallow;

        return $this;
    }

    /**
     * Define the callable that should be invoked on a missing model exception.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function missing($callback)
    {
        $this->options['missing'] = $callback;

        return $this;
    }

    /**
     * Indicate that the resource routes should be scoped using the given binding fields.
     *
     * @param  array  $fields
     * @return \WpStarter\Routing\PendingResourceRegistration
     */
    public function scoped(array $fields = [])
    {
        $this->options['bindingFields'] = $fields;

        return $this;
    }

    /**
     * Register the resource route.
     *
     * @return \WpStarter\Routing\RouteCollection
     */
    public function register()
    {
        $this->registered = true;

        return $this->registrar->register(
            $this->name, $this->controller, $this->options
        );
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}
