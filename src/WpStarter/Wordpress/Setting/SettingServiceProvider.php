<?php

namespace WpStarter\Wordpress\Setting;

use WpStarter\Support\Facades\Artisan;
use WpStarter\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Repository::class, function () {
            return new Repository($this->getOptionKey());
        });
        $this->app->alias(Repository::class, 'setting');
    }

    public function boot(){
        if($this->isRestartQueue()) {
            add_action('update_option_' . $this->getOptionKey(), [$this,'reloadSettings']);
        }
        if($this->isAutosave()) {
            add_action('shutdown', [$this,'saveSettings']);
        }
    }
    public function saveSettings(){
        if($this->app->resolved('setting')) {
            $this->app['setting']->save();
        }
    }
    public function reloadSettings(){
        if($this->app->bound('setting')) {
            $this->app['setting']->reload();
            Artisan::call('queue:restart');
        }
    }

    protected function getOptionKey()
    {
        return $this->app['config']->get('wordpress.settings.option_key', 'ws_settings');
    }
    protected function isAutosave(){
        return $this->app['config']->get('wordpress.settings.autosave', true);
    }
    protected function isRestartQueue(){
        return $this->app['config']->get('wordpress.settings.restart_queue', true);
    }
}
