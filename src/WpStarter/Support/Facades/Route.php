<?php

namespace WpStarter\Support\Facades;

/**
 * @method static \WpStarter\Routing\PendingResourceRegistration apiResource(string $name, string $controller, array $options = [])
 * @method static \WpStarter\Routing\PendingResourceRegistration resource(string $name, string $controller, array $options = [])
 * @method static \WpStarter\Routing\Route any(string $uri, array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route|null current()
 * @method static \WpStarter\Routing\Route delete(string $uri, array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route fallback(array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route get(string $uri, array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route|null getCurrentRoute()
 * @method static \WpStarter\Routing\RouteCollectionInterface getRoutes()
 * @method static \WpStarter\Routing\Route match(array|string $methods, string $uri, array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route options(string $uri, array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route patch(string $uri, array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route permanentRedirect(string $uri, string $destination)
 * @method static \WpStarter\Routing\Route post(string $uri, array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route put(string $uri, array|string|callable|null $action = null)
 * @method static \WpStarter\Routing\Route redirect(string $uri, string $destination, int $status = 302)
 * @method static \WpStarter\Routing\Route substituteBindings(\WpStarter\Support\Facades\Route $route)
 * @method static \WpStarter\Routing\Route view(string $uri, string $view, array $data = [], int|array $status = 200, array $headers = [])
 * @method static \WpStarter\Routing\RouteRegistrar as(string $value)
 * @method static \WpStarter\Routing\RouteRegistrar controller(string $controller)
 * @method static \WpStarter\Routing\RouteRegistrar domain(string $value)
 * @method static \WpStarter\Routing\RouteRegistrar middleware(array|string|null $middleware)
 * @method static \WpStarter\Routing\RouteRegistrar name(string $value)
 * @method static \WpStarter\Routing\RouteRegistrar namespace(string|null $value)
 * @method static \WpStarter\Routing\RouteRegistrar prefix(string $prefix)
 * @method static \WpStarter\Routing\RouteRegistrar scopeBindings()
 * @method static \WpStarter\Routing\RouteRegistrar where(array $where)
 * @method static \WpStarter\Routing\RouteRegistrar withoutMiddleware(array|string $middleware)
 * @method static \WpStarter\Routing\Router|\WpStarter\Routing\RouteRegistrar group(\Closure|string|array $attributes, \Closure|string $routes)
 * @method static \WpStarter\Routing\ResourceRegistrar resourceVerbs(array $verbs = [])
 * @method static string|null currentRouteAction()
 * @method static string|null currentRouteName()
 * @method static void apiResources(array $resources, array $options = [])
 * @method static void bind(string $key, string|callable $binder)
 * @method static void model(string $key, string $class, \Closure|null $callback = null)
 * @method static void pattern(string $key, string $pattern)
 * @method static void resources(array $resources, array $options = [])
 * @method static void substituteImplicitBindings(\WpStarter\Support\Facades\Route $route)
 * @method static boolean uses(...$patterns)
 * @method static boolean is(...$patterns)
 * @method static boolean has(string $name)
 * @method static mixed input(string $key, string|null $default = null)
 *
 * @see \WpStarter\Routing\Router
 */
class Route extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}
