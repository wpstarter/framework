<?php

namespace WpStarter\Wordpress\Shortcode;

use WpStarter\Contracts\Foundation\Application;
use WpStarter\Wordpress\View\Shortcode;

class ShortcodeManager
{
    protected $shortcodes=[];
    protected $app;
    protected $boot_hook=['template_redirect',10];
    protected $booted=false;
    public function __construct(Application $application)
    {
        $this->app=$application;
    }
    public function setBootHook($hook,$priority=10){
        $this->boot_hook=[$hook,$priority];
        return $this;
    }

    public function boot(){
        if(!$this->booted){
            add_action($this->boot_hook[0],[$this,'bootShortcodes'],$this->boot_hook[1]);
        }
    }
    public function bootShortcodes(){
        if(is_singular()){
            if($post=get_post()) {
                foreach ($this->shortcodes as $tag=>$shortcode){
                    if(has_shortcode($post->post_content, $tag)){
                        $this->app->call([$shortcode,'boot']);
                    }
                }
            }
        }
    }

    /**
     * @param string|Shortcode $shortcode
     * @param $callable
     * @return $this
     */
    function add($shortcode,$callable=null){
        if($shortcode instanceof Shortcode){
            $tag=$shortcode->getTag();
            $this->shortcodes[$tag]=$shortcode;
            add_shortcode($tag,function ($attributes,$content='')use($shortcode){
                $shortcode->setAttributes($attributes);
                $shortcode->setContent($content);
                $this->app->call([$shortcode,'mount']);
                $result=$shortcode->render();
                $shortcode->cleanup();
                return $result;
            });
        }else{
            add_shortcode($shortcode,function ($attributes,$content='')use($callable){
                return $this->app->call($callable,[$attributes,$content]);
            });
        }
        return $this;

    }
}