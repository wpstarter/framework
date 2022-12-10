<?php

namespace WpStarter\Wordpress\Http\Response;

use WpStarter\Contracts\Support\Renderable;
use WpStarter\Wordpress\Contracts\HasPostTitle;
use WpStarter\Wordpress\Http\Response;
use WpStarter\Wordpress\Routing\Route;

class Shortcode extends Content implements HasPostTitle
{
    use Response\Concerns\PostTitle;
    public static $defaultShortcode='ws_content';

    /**
     * @var Renderable[]|\Closure[]|mixed[]
     */
    protected $components = [];

    public function __construct($view = null, $data = [], $mergeData = [], $tag=null)
    {
        parent::__construct();
        if(!$tag && ($route=ws_request()->route()) instanceof Route){
            $tag=$route->uri();
        }
        if(!$tag){
            $tag=static::$defaultShortcode;
        }
        if ($tag && $view) {
            $this->add($tag, $view, $data, $mergeData);
        }
    }

    public function getContent($content = null)
    {
        $view=$this->components[static::$defaultShortcode]??'';
        if($view) {
            return Handler::renderView($view);
        }
        return '';
    }

    function all()
    {
        return $this->components;
    }

    /**
     * @param $tag
     * @return Renderable|null
     */
    function view($tag)
    {
        return $this->components[$tag] ?? null;
    }

    /**
     * Add new tag to shortcode list
     * @param $tag
     * @param $view
     * @param $data
     * @param $mergeData
     * @return $this
     */
    function add($tag, $view, $data = [], $mergeData = [])
    {
        $this->push($view, $data, $mergeData, $tag);
        return $this;
    }


    /**
     * @param $shortcode
     * @param $view
     * @param $data
     * @param $mergeData
     * @return static
     */
    public static function make($view = null, $data = [], $mergeData = [], $tag=null)
    {
        return new static($view, $data, $mergeData, $tag);
    }
    public static function setDefaultShortcode($shortcode='ws_content'){
        static::$defaultShortcode=$shortcode;
    }
}