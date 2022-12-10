<?php

namespace WpStarter\Wordpress\Routing;

use WpStarter\Container\Container;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Http\Request;
use WpStarter\Routing\Route as BaseRoute;
use WpStarter\Routing\Router as BaseRouter;

class Router extends BaseRouter
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @param Dispatcher $events
     * @param Container|null $container
     */
    public function __construct(Dispatcher $events, Container $container = null)
    {
        parent::__construct($events, $container);
        $this->routes = new RouteCollection();
    }

    public function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    protected function runRoute(Request $request, BaseRoute $route)
    {
        $response = parent::runRoute($request, $route);
        $route->setResponse($response);
        return $response;
    }

}