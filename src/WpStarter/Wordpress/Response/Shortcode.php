<?php

namespace WpStarter\Wordpress\Response;

use WpStarter\Contracts\Support\Renderable;
use WpStarter\Wordpress\Contracts\HasPostTitle;
use WpStarter\Wordpress\Response;
use WpStarter\Contracts\View\View;
use WpStarter\Wordpress\View\Component;
use WpStarter\Wordpress\View\Factory;

class Shortcode extends Response implements HasPostTitle
{
    use Response\Concerns\PostTitle;
    /**
     * @var Renderable[]|\Closure[]|mixed[]
     */
    protected $shortcodes=[];
    public function __construct($tag=null, $view=null)
    {
        parent::__construct();
        if($tag && $view) {
            $this->shortcodes[$tag] = $view;
        }
    }
    function bootComponent()
    {
        foreach ($this->shortcodes as $view){
            if($view instanceof Component){
                $view->setResponse($this);
                ws_app()->call([$view,'boot']);
            }
        }
    }

    function all(){
        return $this->shortcodes;
    }
    /**
     * @param $tag
     * @return Renderable|null
     */
    function view($tag){
        return $this->shortcodes[$tag]??null;
    }

    /**
     * @param $tag
     * @param $view
     * @param $data
     * @param $mergeData
     * @return mixed|View
     */
    function add($tag, $view, $data = [], $mergeData = []){
        $view=ws_app(Factory::class)->make($view,$data,$mergeData);
        return $this->shortcodes[$tag]=$view;
    }

    /**
     * @param $shortcode
     * @param $view
     * @param $data
     * @param $mergeData
     * @return static
     */
    public static function make($tag, $view, $data = [], $mergeData = []){
        $r= new static();
        $r->add($tag,$view,$data,$mergeData);
        return $r;
    }
}