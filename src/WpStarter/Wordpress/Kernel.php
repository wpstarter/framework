<?php

namespace WpStarter\Wordpress;

use WpStarter\Wordpress\Http\ShortcodeResponse;
use WpStarter\Contracts\Foundation\Application;
use WpStarter\Foundation\Http\Kernel as HttpKernel;
use WpStarter\Routing\Pipeline;
use WpStarter\Routing\Router;
use WpStarter\Wordpress\Routing\Router as ShortcodeRouter;
class Kernel extends HttpKernel
{
    /**
     * @var \WpStarter\Wordpress\Application
     */
    protected $app;
    protected $earlyBootstrapers=[
        \WpStarter\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \WpStarter\Foundation\Bootstrap\LoadConfiguration::class,
        \WpStarter\Wordpress\Bootstrap\HandleExceptions::class,
        \WpStarter\Foundation\Bootstrap\RegisterFacades::class,
    ];
    /**
     * The bootstrap classes for the application.
     *
     * @var string[]
     */
    protected $bootstrappers = [
        \WpStarter\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \WpStarter\Foundation\Bootstrap\LoadConfiguration::class,
        \WpStarter\Wordpress\Bootstrap\HandleExceptions::class,
        \WpStarter\Foundation\Bootstrap\RegisterFacades::class,
        \WpStarter\Foundation\Bootstrap\RegisterProviders::class,
        \WpStarter\Foundation\Bootstrap\BootProviders::class,
    ];
    protected $wpRouter;

    public function __construct(Application $app, Router $router, ShortcodeRouter $wpRouter)
    {
        $this->wpRouter=$wpRouter;
        parent::__construct($app, $router);
    }

    function registerWpHandler(){
        $this->wpRouter->registerShortcodes($this->app['request']);
        add_action('template_redirect',function(){
            $this->handleWp($this->app['request']);
        },1);
    }



    function handleWp($request){
        try {
            $request->setRouteNotFoundHttpException(false);
            $response = $this->wpSendRequestThroughRouter($request);
        }catch (\Throwable $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        }

        if(!$request->isNotFoundHttpExceptionFromRoute()){
            //We just ignore no route matching exception and allow application continue running
            if($response instanceof ShortcodeResponse){
                //Our responses converted from StringAble, we only send headers for them
                $response->sendHeaders();
            }else{//Normal response from controller middleware, etc...
                $response->send();
                $this->terminate($request, $response);
                exit;
            }
        }
        add_action('shutdown',function()use($request,$response){
            $this->terminate($request, $response);
        });

    }
    /**
     * Send the given request through the middleware / router.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return \WpStarter\Http\Response
     */
    protected function wpSendRequestThroughRouter($request)
    {
        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
            ->then($this->dispatchToWpRouter());
    }
    function dispatchToWpRouter($route=null){
        return function($request)use($route){
            return $this->wpRouter->dispatch($request);
        };
    }

    /**
     * Sync the current state of the middleware to the router.
     *
     * @return void
     */
    protected function syncMiddlewareToRouter()
    {
        parent::syncMiddlewareToRouter();
        $this->wpRouter->middlewarePriority = $this->middlewarePriority;

        foreach ($this->middlewareGroups as $key => $middleware) {
            $this->wpRouter->middlewareGroup($key, $middleware);
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $this->wpRouter->aliasMiddleware($key, $middleware);
        }

    }



    function earlyBootstrap(){
        foreach ($this->earlyBootstrapers as $bootstraper){
            $this->app->bootstrapOne($bootstraper);
        }
    }
}