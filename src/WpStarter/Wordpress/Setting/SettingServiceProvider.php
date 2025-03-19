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
            add_action('update_option_' . $this->getOptionKey(), [$this,'reloadSettings']);
        }
        if($this->autoSave) {
            add_action('shutdown', [$this,'saveSettings']);
        }
    }
    public function saveSettings(){
        if($this->app->bound('setting')) {
            $this->app['setting']->save();
        }
    }
    public function reloadSettings(){
        if($this->app->bound('setting')) {
            $this->app['setting']->reload();
            Artisan::call('queue:restart');
        }
    }

    abstract protected function getOptionKey();
}
