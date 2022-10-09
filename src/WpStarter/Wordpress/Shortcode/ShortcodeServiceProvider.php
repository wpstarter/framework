<?php
namespace WpStarter\Wordpress\Shortcode;

use WpStarter\Support\ServiceProvider;
use WpStarter\Wordpress\Contracts\Kernel;

class ShortcodeServiceProvider extends ServiceProvider
{
    function register()
    {
        $this->app->singleton('wp.router',function($app){
            return new Router($app['events'], $app);
        });
        $this->app->alias('wp.router',Router::class);
    }

    function boot(){
        add_action('wp_loaded',function(){
            if($this->app->bound(Kernel::class)) {
                $this->app->make(Kernel::class)->register();
            }
        });
    }
}