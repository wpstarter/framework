<?php

namespace WpStarter\Wordpress\Response;

class Page extends Content
{
    protected $hook;
    public function on($hook,$priority=10){
        $this->hook=[$hook,$priority];
        return $this;
    }
    public function getHook(){
        return $this->hook;
    }
}