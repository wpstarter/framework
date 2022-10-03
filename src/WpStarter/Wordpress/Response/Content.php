<?php

namespace WpStarter\Wordpress\Response;

use WpStarter\Contracts\Support\Renderable;
use WpStarter\Contracts\View\View;
use WpStarter\Support\Arr;
use WpStarter\Wordpress\Contracts\HasPostTitle;
use WpStarter\Wordpress\Response;
use WpStarter\Wordpress\View\Component;
use WpStarter\Wordpress\View\Factory;


/**
 * @mixin View
 */
class Content extends Response implements HasPostTitle
{
    use Response\Concerns\PostTitle;
    protected $components;
    public function __construct($view=null){
        parent::__construct();
        if($view) {
            $this->components = $view;
        }
    }
    function bootComponent(){
        if(!$this->componentBooted) {
            foreach (Arr::wrap($this->components) as $view) {
                if ($view instanceof Component) {
                    $view->setResponse($this);
                    ws_app()->call([$view, 'boot']);
                }
            }
            $this->componentBooted=true;
        }
    }
    function mountComponent(){
        if(!$this->componentMounted) {
            foreach (Arr::wrap($this->components) as $view) {
                if ($view instanceof Component) {
                    ws_app()->call([$view, 'mount']);
                }
            }
            $this->componentMounted=true;
        }
    }

    function getContent($content=null){
        $buffer='';
        foreach (Arr::wrap($this->components) as $view) {
            $buffer.=Handler::renderView($view);
        }
        if($buffer){
            return $buffer;
        }
        return $content;
    }
    public static function make($view, $data = [], $mergeData = []){
        $view=ws_app(Factory::class)->make($view,$data,$mergeData);
        return new static($view);
    }
    public function __call($method, $parameters)
    {
        foreach (Arr::wrap($this->components) as $view){
            call_user_func_array([$view,$method],$parameters);
        }
        return $this;
    }
}