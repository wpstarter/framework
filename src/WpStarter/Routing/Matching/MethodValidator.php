<?php

namespace WpStarter\Routing\Matching;

use WpStarter\Http\Request;
use WpStarter\Routing\Route;

class MethodValidator implements ValidatorInterface
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
        return in_array($request->getMethod(), $route->methods());
    }
}
