<?php

namespace WpStarter\Wordpress\Routing;

use WpStarter\Routing\Matching\HostValidator;
use WpStarter\Routing\Matching\MethodValidator;
use WpStarter\Routing\Matching\SchemeValidator;
use WpStarter\Support\ServiceProvider;
use WpStarter\Wordpress\Routing\Matching\HookValidator;
use WpStarter\Wordpress\Routing\Matching\ShortcodeValidator;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * The callback that should be used to load the application's routes.
     *
     * @var \Closure|null
     */
    protected static $loadRoutesUsing;

    public function register()
    {
        $this->app->singleton('wp.router', function ($app) {
            return new Router($app['events'], $app);
        });
        $this->app->alias('wp.router', Router::class);
        $this->app->booted(function(){
            $this->loadRoutes();
        });
    }

    public function boot()
    {
        \WpStarter\Routing\Route::mixin(new RouteHook());
        \WpStarter\Routing\Route::$validators=array_merge(\WpStarter\Routing\Route::getValidators(), [new HookValidator()]);
        Route::$validators=[
            new ShortcodeValidator, new MethodValidator,
            new SchemeValidator, new HostValidator,
        ];
    }
    /**
     * Load the application routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        if (! is_null(static::$loadRoutesUsing)) {
            $this->app->call(static::$loadRoutesUsing);
        }
    }

    /**
     * Register the callback that will be used to load the application's routes.
     *
     * @param  \Closure|null  $routesCallback
     * @return void
     */
    public static function loadRoutesUsing(?Closure $routesCallback)
    {
        static::$loadRoutesUsing = $routesCallback;
    }


}
