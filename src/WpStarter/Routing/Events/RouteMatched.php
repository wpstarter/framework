<?php

namespace WpStarter\Routing\Events;

class RouteMatched
{
    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Routing\Route  $route  The route instance.
     * @param  \WpStarter\Http\Request  $request  The request instance.
     */
    public function __construct(
        public $route,
        public $request,
    ) {
    }
}
