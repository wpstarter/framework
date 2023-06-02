<?php

namespace WpStarter\Wordpress;

use WpStarter\Contracts\Foundation\Application;
use WpStarter\Foundation\Http\Kernel as HttpKernel;
use WpStarter\Routing\Pipeline;
use WpStarter\Routing\Router;
use WpStarter\Wordpress\Routing\Router as ShortcodeRouter;

class Kernel extends HttpKernel
{
    protected $wpHandleHook=['template_redirect',1];
    /**
     * @var \WpStarter\Wordpress\Application
     */
    protected $app;
    protected $earlyBootstrapers = [
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
        $this->wpRouter = $wpRouter;
        parent::__construct($app, $router);
    }

    public function handle($request, $processResponse=false)
    {
        $response = parent::handle($request);
        if($request->isNotFoundHttpExceptionFromRoute()) {
            //Not found response and come from no route match...
            if($processResponse) {
                $this->registerWpHandler($request);
            }else{//Expected to return response
                $response=$this->handleWp($request,$processResponse);
            }
        }else{
            if($processResponse) {
                $this->processResponse($request, $response);
            }
        }
        return $response;
    }

    /**
     * Process response
     * @param $request
     * @param $response
     * @return void
     */
    protected function processResponse($request, $response){
        //Not a not found response from router
        if($response instanceof \WpStarter\Wordpress\Http\Response){
            //Got a WordPress response, process it
            $handler=$this->app->make(\WpStarter\Wordpress\Http\Response\Handler::class);
            /**
             * @var \WpStarter\Wordpress\Http\Response\Handler $handler
             */
            $handler->handle($this,$request,$response);
        }else {//Normal response
            $response->send();
            $this->terminate($request, $response);
            die;
        }

    }

    function registerWpHandler($request)
    {
        $hook=(array)$this->wpHandleHook;
        add_action($hook[0]??'template_redirect', function ()use($request) {
            $this->handleWp($request,true);
        }, $hook[1]??1);
    }

    /**
     * Handle WordPress Route
     * @param $request
     * @param bool $processResponse
     * @return \Symfony\Component\HttpFoundation\Response|\WpStarter\Http\Response
     */
    function handleWp($request, $processResponse=false)
    {
        try {
            $request->setRouteNotFoundHttpException(false);
            $response = $this->wpSendRequestThroughRouter($request);
        } catch (\Throwable $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        }
        if (!$request->isNotFoundHttpExceptionFromRoute()) {
            if($processResponse) {
                $this->processResponse($request, $response);
            }
        }//No roure match continue to wp
        return $response;

    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param \WpStarter\Http\Request $request
     * @return \WpStarter\Http\Response
     */
    protected function wpSendRequestThroughRouter($request)
    {
        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
            ->then($this->dispatchToWpRouter());
    }

    function dispatchToWpRouter($route = null)
    {
        return function ($request) use ($route) {
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


    function earlyBootstrap()
    {
        foreach ($this->earlyBootstrapers as $bootstraper) {
            $this->app->bootstrapOne($bootstraper);
        }
    }
}
