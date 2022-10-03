<?php

namespace WpStarter\Wordpress\Response;

use WpStarter\Contracts\Support\Renderable;
use WpStarter\Wordpress\Contracts\HasPostTitle;
use WpStarter\Wordpress\Response;
use WpStarter\Contracts\View\View;
use WpStarter\Wordpress\View\Component;
use WpStarter\Wordpress\View\Factory;

class Shortcode extends Content implements HasPostTitle
{
    use Response\Concerns\PostTitle;
    /**
     * @var Renderable[]|\Closure[]|mixed[]
     */
    protected $components=[];
    public function __construct($tag=null, $view=null)
    {
        parent::__construct();
        if($tag && $view) {
            $this->components[$tag] = $view;
        }
    }


    function all(){
        return $this->components;
    }
    /**
     * @param $tag
     * @return Renderable|null
     */
    function view($tag){
        return $this->components[$tag]??null;
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
        return $this->components[$tag]=$view;
    }

    /**
     * @param $shortcode
     * @param $view
     * @param $data
     * @param $mergeData
     * @return static
     */
    public static function make($tag, $view=null, $data = [], $mergeData = []){
        $r= new static();
        $r->add($tag,$view,$data,$mergeData);
        return $r;
    }
}