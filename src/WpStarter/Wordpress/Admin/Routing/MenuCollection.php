<?php

namespace WpStarter\Wordpress\Admin\Routing;

use WpStarter\Routing\RouteCollection;

class MenuCollection extends RouteCollection
{
    protected function addToCollections($route)
    {
        $domainAndUri = $route->getDomain().$route->getAction('parent').$route->uri();

        foreach ($route->methods() as $method) {
            $this->routes[$method][$domainAndUri] = $route;
        }

        $this->allRoutes[$method.$domainAndUri] = $route;
    }
}
