<?php

namespace WpStarter\Wordpress\Bootstrap;

trait HasEarlyBootstrapers
{
    protected $earlyBootstrappers = [
        \WpStarter\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \WpStarter\Foundation\Bootstrap\LoadConfiguration::class,
        \WpStarter\Wordpress\Bootstrap\HandleExceptions::class,
        \WpStarter\Foundation\Bootstrap\RegisterFacades::class,
    ];

    function earlyBootstrap()
    {
        if(!$this->app->hasBeenEarlyBootstrapped()) {
            $this->app->earlyBootstrapWith($this->earlyBootstrappers());
        }
    }
    protected function earlyBootstrappers()
    {
        return $this->earlyBootstrappers;
    }
    protected function bootstrappers()
    {
        $bootstrappers = parent::bootstrappers();
        if($this->app->hasBeenEarlyBootstrapped()) {
            //Remove early bootstrapper from bootstrapper list
            $bootstrappers=array_diff($bootstrappers, $this->earlyBootstrappers);
        }
        return $bootstrappers;
    }
}
