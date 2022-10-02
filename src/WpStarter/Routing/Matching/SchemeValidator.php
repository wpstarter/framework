<?php

namespace WpStarter\Routing\Matching;

use WpStarter\Http\Request;
use WpStarter\Routing\Route;

class SchemeValidator implements ValidatorInterface
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
        if ($route->httpOnly()) {
            return ! $request->secure();
        } elseif ($route->secure()) {
            return $request->secure();
        }

        return true;
    }
}
