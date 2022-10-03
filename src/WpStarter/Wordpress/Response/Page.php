<?php

namespace WpStarter\Wordpress\Response;

class Page extends Content
{
    protected $hook;
    protected $titleParts;
    function withTitleParts($partOrResolver){
        $this->titleParts=$partOrResolver;
        return $this;
    }
    function getTitleParts($parts){
        if($this->titleParts instanceof \Closure){
            $parts=call_user_func($this->titleParts,$parts);
        }elseif(is_array($this->titleParts)){
            $parts=$this->titleParts;
        }
        return $parts;
    }
    public function on($hook,$priority=10){
        $this->hook=[$hook,$priority];
        return $this;
    }
    public function onWpLoaded($priority=10){
        return $this->on('wp_loaded',$priority);
    }
    public function onWp($priority=10){
        return $this->on('wp',$priority);
    }
    public function onTemplateRedirect($priority=10){
        return $this->on('template_redirect',$priority);
    }
    public function getHook(){
        return $this->hook;
    }
}