<?php

namespace WpStarter\Wordpress\Setting;

use WpStarter\Support\ServiceProvider;

abstract class SettingServiceProvider extends ServiceProvider
{
    function register()
    {
        $this->app->singleton(Repository::class,function(){
            return new Repository($this->getOptionKey());
        });
        $this->app->alias(Repository::class,'setting');
    }
    abstract protected function getOptionKey();
}