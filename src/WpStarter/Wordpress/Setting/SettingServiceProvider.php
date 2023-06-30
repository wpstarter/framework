<?php

namespace WpStarter\Wordpress\Setting;

use WpStarter\Support\Facades\Artisan;
use WpStarter\Support\ServiceProvider;

abstract class SettingServiceProvider extends ServiceProvider
{
    protected $autoRestartQueue=true;
    protected $autoSave=true;
    function register()
    {
        $this->app->singleton(Repository::class, function () {
            return new Repository($this->getOptionKey());
        });
        $this->app->alias(Repository::class, 'setting');
    }

    public function boot(){
        if($this->autoRestartQueue) {
            add_action('update_option_' . $this->getOptionKey(), function () {
                Artisan::call('queue:restart');
            });
        }
        if($this->autoSave) {
            add_action('shutdown', function () {
                $this->app['setting']->save();
            });
        }
    }

    abstract protected function getOptionKey();
}
