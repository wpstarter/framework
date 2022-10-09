<?php

namespace WpStarter\Wordpress\Shortcode;

use WpStarter\Contracts\Debug\ExceptionHandler;
use WpStarter\Contracts\Foundation\Application;
use WpStarter\Foundation\Http\Kernel as HttpKernel;
use WpStarter\Routing\Pipeline;
use Throwable;
use WpStarter\Wordpress\Contracts\Kernel as BaseKernel;

class Kernel extends HttpKernel implements BaseKernel
{
    public function __construct(Application $app, Router $router)
    {
        parent::__construct($app, $router);
        $this->router=$router;
    }

    function register(){
        $this->router->registerShortcodes($this->app['request']);
        add_action('template_redirect',function(){
            $this->handle($this->app['request']);
        },-1);
    }



    function handle($request){
        try {
            $request->setRouteNotFoundHttpException(false);
            $response = $this->sendRequestThroughRouter($request);
        }catch (\Throwable $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        }

        if(!$request->isNotFoundHttpExceptionFromRoute()){
            //We just ignore no route matching exception and allow application continue running
            if($response instanceof Response){
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
    protected function sendRequestThroughRouter($request)
    {
        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
            ->then($this->dispatchToRouter());
    }
    function dispatchToRouter($route=null){
        return function($request)use($route){
            return $this->router->dispatch($request);
        };
    }
    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function reportException(Throwable $e)
    {
        $this->app[ExceptionHandler::class]->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException($request, Throwable $e)
    {
        return $this->app[ExceptionHandler::class]->render($request, $e);
    }

    /**
     * Sync the current state of the middleware to the router.
     *
     * @return void
     */
    protected function syncMiddlewareToRouter()
    {
        $this->router->middlewarePriority = $this->middlewarePriority;

        foreach ($this->middlewareGroups as $key => $middleware) {
            $this->router->middlewareGroup($key, $middleware);
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $this->router->aliasMiddleware($key, $middleware);
        }
    }
}