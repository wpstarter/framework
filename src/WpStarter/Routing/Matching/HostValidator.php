<?php

namespace WpStarter\Routing\Matching;

use WpStarter\Http\Request;
use WpStarter\Routing\Route;

class HostValidator implements ValidatorInterface
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
        $hostRegex = $route->getCompiled()->getHostRegex();

        if (is_null($hostRegex)) {
            return true;
        }

        return preg_match($hostRegex, $request->getHost());
    }
}
