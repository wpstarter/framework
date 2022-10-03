<?php

namespace WpStarter\Wordpress\Response;

use WpStarter\Contracts\Http\Kernel;
use WpStarter\Contracts\Support\Renderable;
use WpStarter\Http\Request;
use WpStarter\Wordpress\Contracts\HasGetTitle;
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
    function handle(Kernel $kernel,Request $request,Response $response){
        $this->kernel=$kernel;
        $this->request=$request;
        $this->response=$response;
        $response->bootComponent();
        $response->sendHeaders();//Header should be sent as soon as possible
        if($response instanceof HasGetTitle) {
            add_filter('the_title', function ($content) use ($response) {
                return $response->getTitle($content);
            });
        }
        if($response instanceof Page){
            list($hook,$priority)=$response->getHook();
            if(!$hook || did_action($hook)) {
                $this->sendPageResponse($kernel,$request,$response);
            }else{
                add_action($hook,function()use($kernel,$request,$response){
                    $this->sendPageResponse($kernel,$request,$response);
                },$priority);
            }
        }else {
            if ($response instanceof Content) {
                add_filter('the_content', function ($content) use ($response) {
                    return $response->getContent($content);
                });
            } elseif ($response instanceof Shortcode) {
                foreach ($response->all() as $tag => $view) {
                    add_shortcode($tag, function () use ($view) {
                        return static::renderView($view);
                    });
                }
            }
            $this->registerTerminateOnShutdown();
        }
    }
    public static function renderView($view){
        if($view instanceof Component){
            $view->mount();
        }
        if($view instanceof Renderable) {
            return $view->render();
        }
        if(method_exists($view,'__toString')){
            return $view->__toString();
        }
    }
    protected function registerTerminateOnShutdown(){
        add_action('wp_shutdown',[$this,'terminate']);
    }
    public function terminate(){
        $this->kernel->terminate($this->request, $this->response);
    }
    function sendPageResponse(Kernel $kernel, Request $request, Page $response){
        add_filter('document_title_parts',function($titleParts)use($response){
            return $response->getTitleParts($titleParts);
        },10000);
        add_filter('document_title',function($title) use($response){
            return $response->getTitle($title);
        },10000);
        $response->send();
        $kernel->terminate($request, $response);
        die;
    }
}