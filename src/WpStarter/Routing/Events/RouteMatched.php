<?php

namespace WpStarter\Routing\Events;

class RouteMatched
{
    /**
     * The route instance.
     *
     * @var \WpStarter\Routing\Route
     */
    public $route;

    /**
     * The request instance.
     *
     * @var \WpStarter\Http\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Routing\Route  $route
     * @param  \WpStarter\Http\Request  $request
     * @return void
     */
    public function __construct($route, $request)
    {
        $this->route = $route;
        $this->request = $request;
    }
}
