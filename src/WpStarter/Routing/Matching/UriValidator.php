<?php

namespace WpStarter\Routing\Matching;

use WpStarter\Http\Request;
use WpStarter\Routing\Route;

class UriValidator implements ValidatorInterface
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
        $path = rtrim($request->getPathInfo(), '/') ?: '/';

        return preg_match($route->getCompiled()->getRegex(), rawurldecode($path));
    }
}
