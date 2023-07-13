<?php

namespace WpStarter\Wordpress\Admin\Routing\Matching;

use WpStarter\Http\Request;
use WpStarter\Routing\Matching\ValidatorInterface;
use WpStarter\Routing\Route;

class ScreenIdValidator implements ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \WpStarter\Routing\Route  $route
     * @param  \WpStarter\Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        global $current_screen;
        return $route->hookSuffix==$current_screen->id;
    }
}