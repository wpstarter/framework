<?php

namespace WpStarter\Wordpress\Admin\Facades;

use WpStarter\Support\Facades\Facade;
use WpStarter\Wordpress\Admin\Routing\Menu;

/**
 * @method static Menu add($slug, $callback, $capability = 'read', $title ='' ,$page_title = '', $icon = '', $position = null)
 * @method static Menu current()
 * @method static \WpStarter\Wordpress\Admin\Routing\RouteRegistrar controller(string $controller)
 * @method static \WpStarter\Wordpress\Admin\Routing\RouteRegistrar middleware(array|string|null $middleware=null)
 * @method static \WpStarter\Wordpress\Admin\Routing\RouteRegistrar namespace(string|null $value=null)
 * @method static void group(\Closure|string|array $attributes, \Closure|string $routes)
 */
class Route extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.admin.router';
    }
}