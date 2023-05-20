<?php

namespace WpStarter\Wordpress\Services;

class PluginsLoader
{
    public function __construct()
    {

    }
    protected function shouldRun(){

    }
    function run(){
        add_filter('option_active_plugins',[$this,'filterPlugins']);
    }
    function filterPlugins($plugins){

    }
}
