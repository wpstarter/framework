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
    public function __construct($tag, $view=null, $data = [], $mergeData = [])
    {
        parent::__construct();
        if($tag && $view) {
            $this->add($tag,$view,$data,$mergeData);
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
     * Add new tag to shortcode list
     * @param $tag
     * @param $view
     * @param $data
     * @param $mergeData
     * @return $this
     */
    function add($tag, $view, $data = [], $mergeData = []){
        $this->push($view,$data,$mergeData,$tag);
        return $this;
    }


    /**
     * @param $shortcode
     * @param $view
     * @param $data
     * @param $mergeData
     * @return static
     */
    public static function make($tag, $view=null, $data = [], $mergeData = []){
        return new static($tag,$view,$data,$mergeData);
    }
}