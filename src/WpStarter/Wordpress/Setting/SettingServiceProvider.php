<?php

namespace WpStarter\Wordpress\Setting;

use WpStarter\Support\Facades\Artisan;
use WpStarter\Support\ServiceProvider;

abstract class SettingServiceProvider extends ServiceProvider
{
    function register()
    {
        $this->app->singleton(Repository::class, function () {
            return new Repository($this->getOptionKey());
        });
        $this->app->alias(Repository::class, 'setting');
    }

    public function boot(){
        add_action('update_option_'.$this->getOptionKey(),function (){
            Artisan::call('queue:restart');
        });
    }

    abstract protected function getOptionKey();
}
