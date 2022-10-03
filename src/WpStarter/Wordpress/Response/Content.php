<?php

namespace WpStarter\Wordpress\Response;

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
    protected $components=[];
    public function __construct($view=null, $data = [], $mergeData = []){
        parent::__construct();
        if($view) {
            $this->push($view,$data,$mergeData);
        }
    }
    function bootComponent(){
        if(!$this->componentBooted) {
            foreach ($this->components as $view) {
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
            foreach ($this->components as $view) {
                if ($view instanceof Component) {
                    ws_app()->call([$view, 'mount']);
                }
            }
            $this->componentMounted=true;
        }
    }

    function getContent($content=null){
        $buffer='';
        foreach ($this->components as $view) {
            $buffer.=Handler::renderView($view);
        }
        if($buffer){
            return $buffer;
        }
        return $content;
    }

    /**
     * @param $view
     * @param $data
     * @param $mergeData
     * @param $key
     * @return mixed|View|Content
     */
    function push($view, $data = [], $mergeData = [], $key=null){
        $view=ws_app(Factory::class)->make($view,$data,$mergeData);
        if($key) {
            $this->components[$key] = $view;
        }else{
            $this->components[]=$view;
        }
        return $view;
    }
    function getComponents(){
        return $this->components;
    }

    /**
     * @param array $components
     * @return $this
     */
    function setComponents(array $components){
        $this->components=$components;
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    function remove($key){
        unset($this->components[$key]);
        return $this;
    }
    function get($key){
        return $this->components[$key]??null;
    }
    function append($content,$key=null){
        $this->push($content,[],[],$key);
        return $this;
    }
    public static function make($view, $data = [], $mergeData = []){
        return new static($view, $data, $mergeData );
    }
    public function __call($method, $parameters)
    {
        foreach ($this->components as $view){
            if(is_object($view)) {
                call_user_func_array([$view, $method], $parameters);
            }
        }
        return $this;
    }
}