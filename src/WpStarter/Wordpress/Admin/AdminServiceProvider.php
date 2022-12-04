<?php

namespace WpStarter\Wordpress\Admin;

use WpStarter\Support\ServiceProvider;
use WpStarter\Wordpress\Admin\Routing\Router;
use WpStarter\Wordpress\Admin\Contracts\Kernel;
class AdminServiceProvider extends ServiceProvider
{
    function register()
    {
        $this->app->singleton('wp.admin.router', function ($app) {
            return new Router($app['events'], $app);
        });
        $this->app->alias('wp.admin.router', Router::class);
    }

    function boot()
    {
        if(!is_wp()){
            return ;
        }
        if($this->app->bound(Kernel::class)) {
            $this->app->make(Kernel::class)->handle($this->app['request']);
            $this->loadViewsFrom(__DIR__ . '/resources/views', 'wp.admin');
        }
    }
}