<?php

namespace WpStarter\Wordpress\Routing;

use WpStarter\Routing\RouteCollection as BaseRouteCollection;

class RouteCollection extends BaseRouteCollection
{
    protected function matchAgainstRoutes(array $routes, $request, $includingMethod = true)
    {
        if (is_singular()) {
            if ($post = get_post()) {
                foreach ($routes as $route) {
                    if (has_shortcode($post->post_content, $route->uri())) {
                        return $route;
                    }
                }
            }
        }
    }
}