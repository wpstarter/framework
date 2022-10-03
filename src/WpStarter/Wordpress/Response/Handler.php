<?php

namespace WpStarter\Wordpress\Response;

use WpStarter\Container\BoundMethod;
use WpStarter\Contracts\Foundation\Application;
use WpStarter\Contracts\Http\Kernel;
use WpStarter\Contracts\Support\Renderable;
use WpStarter\Http\Request;
use WpStarter\Wordpress\Contracts\HasPostTitle;
use WpStarter\Wordpress\Response;
use WpStarter\Wordpress\View\Component;

class Handler
{
    /**
     * @var Kernel
     */
    protected $kernel;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response|Page|Content
     */
    protected $response;

    protected $customResponseHandlers=[];
    protected $app;
    public function __construct(Application $app)
    {
        $this->app=$app;
    }

    function handle(Kernel $kernel,Request $request,Response $response){
        $this->kernel=$kernel;
        $this->request=$request;
        $this->response=$response;
        $response->bootComponent();
        $response->sendHeaders();//Header should be sent as soon as possible
        $this->setupTitleFilters($response);

        if($response instanceof Page){
            list($hook,$priority)=$response->getHook();
            if(!$hook || did_action($hook)) {
                $this->sendPageResponse($kernel,$request,$response);
            }else{
                add_action($hook,function()use($kernel,$request,$response){
                    $this->sendPageResponse($kernel,$request,$response);
                },$priority);
            }
        }elseif($response instanceof Shortcode) {
            $this->registerTerminateOnShutdown();
            foreach ($response->all() as $tag => $view) {
                add_shortcode($tag, function () use ($view,$response) {
                    $response->mountComponent();
                    return static::renderView($view);
                });
            }
        }elseif ($response instanceof Content) {
            $this->registerTerminateOnShutdown();
            add_filter('the_content', function ($content) use ($response) {
                $response->mountComponent();
                return $response->getContent($content);
            });
        }else{
            foreach ($this->customResponseHandlers as $customResponseHandler){
                if($customResponseHandler instanceof \Closure){
                    $handled=$customResponseHandler($kernel,$request,$response);
                }else {
                    $handled=$this->app->make($customResponseHandler)->handle($kernel, $request, $response);
                }
                if($handled){
                    break;
                }
            }
        }
    }
    public function addCustomHandler($handler){
        $this->customResponseHandlers[]=$handler;
        return $this;
    }
    public static function renderView($view){
        if($view instanceof Renderable) {
            return $view->render();
        }
        if(method_exists($view,'__toString')){
            return $view->__toString();
        }
    }
    protected function registerTerminateOnShutdown(){
        add_action('shutdown',[$this,'terminate']);
    }
    public function terminate(){
        $this->kernel->terminate($this->request, $this->response);
    }
    function sendPageResponse(Kernel $kernel, Request $request, Page $response){
        $response->mountComponent();
        $response->send();
        $kernel->terminate($request, $response);
        die;
    }
    protected function setupTitleFilters(Response $response){
        if($response instanceof HasPostTitle) {
            add_filter('the_title', function ($postTitle) use ($response) {
                $response->mountComponent();
                return $response->getPostTitle($postTitle);
            },10000);
        }
        add_filter('document_title_parts',function($titleParts)use($response){
            $response->mountComponent();
            return $response->getTitleParts($titleParts);
        },10000);
        add_filter('document_title',function($title) use($response){
            $response->mountComponent();
            return $response->getDocumentTitle($title);
        },10000);
    }
}