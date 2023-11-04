<?php

namespace WpStarter\Wordpress\Routing;

use WpStarter\Support\ServiceProvider;
use WpStarter\Wordpress\Routing\Matching\HookValidator;

class RoutingServiceProvider extends ServiceProvider
{
    function register()
    {
        $this->app->singleton('wp.router', function ($app) {
            return new Router($app['events'], $app);
        });
        $this->app->alias('wp.router', Router::class);
    }

    function boot()
    {
        \WpStarter\Routing\Route::mixin(new RouteHook());
        \WpStarter\Routing\Route::$validators=array_merge(\WpStarter\Routing\Route::getValidators(), [new HookValidator()]);
    }
}
