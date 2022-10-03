<?php

namespace WpStarter\Wordpress\Response;

use WpStarter\Contracts\Support\Renderable;
use WpStarter\Contracts\View\View;
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
    protected $view;
    public function __construct($view){
        parent::__construct();
        $this->view=$view;
    }
    function bootComponent(){
        if($this->view instanceof Component){
            $this->view->setResponse($this);
            ws_app()->call([$this->view,'boot']);
        }
    }

    function getContent($content=null){
        $rendered=Handler::renderView($this->view);
        if($rendered){
            return $rendered;
        }
        return $content;
    }
    public static function make($view, $data = [], $mergeData = []){
        $view=ws_app(Factory::class)->make($view,$data,$mergeData);
        return new static($view);
    }
    public function __call($method, $parameters)
    {
        call_user_func_array([$this->view,$method],$parameters);
        return $this;
    }
}