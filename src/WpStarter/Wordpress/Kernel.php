<?php

namespace WpStarter\Wordpress;

use WpStarter\Contracts\Foundation\Application;
use WpStarter\Foundation\Http\Kernel as HttpKernel;
use WpStarter\Http\Request;
use WpStarter\Routing\Pipeline;
use WpStarter\Routing\Router;
use WpStarter\Support\Facades\Facade;
use WpStarter\Wordpress\Bootstrap\HasEarlyBootstrappers;
use WpStarter\Wordpress\Routing\Router as ShortcodeRouter;

class Kernel extends HttpKernel
{
    use HasEarlyBootstrappers;
    protected $wpHandleHook=['template_redirect',1];
    /**
     * @var \WpStarter\Wordpress\Application
     */
    protected $app;
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
        $request->setRouteNotFoundHttpException(false);
        $response = parent::handle($request);
        if(!$request->isNotFoundHttpExceptionFromRoute()) {
            //Got final response process if requested
            if($processResponse) {
                $this->processResponse($request, $response);
            }
        }else{
            //Continue to run wp route stack
            $response=$this->handleWp($request,$response,$processResponse);
        }
        return $response;
    }

    function handleWp($request, $response, $processResponse=false){
        if($processResponse) {
            //Requested to process response, we run at target hook
            $this->handleAndProcessWpAtTargetHook($request);
        }else{
            //No process return the response
            $response=$this->handleWpRoute($request);
        }
        return $response;
    }

    function handleAndProcessWpAtTargetHook($request): void
    {
        $hook=(array)$this->wpHandleHook;
        add_action($hook[0]??'template_redirect', function ()use($request) {
            $response=$this->handleWpRoute($request);
            if(!$request->isNotFoundHttpExceptionFromRoute()){
                //Got a response, process it
                $this->processResponse($request, $response);
            }
        }, $hook[1]??1);
    }

    /**
     * Handle WordPress Route
     * @param $request
     * @param bool $processResponse
     * @return \Symfony\Component\HttpFoundation\Response|\WpStarter\Http\Response
     */
    function handleWpRoute($request)
    {
        try {
            $request->setRouteNotFoundHttpException(false);
            $response = $this->wpSendRequestThroughRouter($request);
        } catch (\Throwable $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        }
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
        $this->app->instance('request', $request);

        Facade::clearResolvedInstance('request');

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

        foreach (array_merge($this->routeMiddleware, $this->middlewareAliases) as $key => $middleware) {
            $this->wpRouter->aliasMiddleware($key, $middleware);
        }

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
}
