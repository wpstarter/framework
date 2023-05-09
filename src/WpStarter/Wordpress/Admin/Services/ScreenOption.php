<?php

namespace WpStarter\Wordpress\Admin\Services;

use WpStarter\Support\Arr;

class ScreenOption
{
    protected $priority=10;
    static protected $booted=false;
    protected $options=[];
    public function __construct()
    {
        $this->bootIfNotBooted();
    }
    protected function bootIfNotBooted(){
        if(static::$booted || ! function_exists('add_action')){
            return ;
        }
        $this->boot();
        static::$booted=true;
    }
    protected function boot(){
        add_action('check_admin_referer',function($action){
            if($action==='screen-options-nonce'){
                add_filter('set-screen-option', [$this,'processScreenOption'] , $this->priority, 3);
            }
        });
    }

    function processScreenOption($screen_option, $option, $value) {
        if (isset($this->options[$option])) {
            $optionCallback=$this->options[$option];
            if($optionCallback instanceof \Closure){
                return $optionCallback($value);
            }
            return $value;
        }
        return $screen_option;
    }
    function add($options,$valueCallback=true){
        foreach (Arr::wrap($options) as $option) {
            $this->options[$option] = $valueCallback;
        }
        return $this;
    }
}
