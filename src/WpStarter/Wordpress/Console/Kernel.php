<?php

namespace WpStarter\Wordpress\Console;

use WpStarter\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $earlyBootstrapers=[
        \WpStarter\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \WpStarter\Foundation\Bootstrap\LoadConfiguration::class,
        \WpStarter\Wordpress\Bootstrap\HandleExceptions::class,
        \WpStarter\Foundation\Bootstrap\RegisterFacades::class,
    ];
    /**
     * The bootstrap classes for the application.
     *
     * @var string[]
     */
    protected $bootstrappers = [
        \WpStarter\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \WpStarter\Foundation\Bootstrap\LoadConfiguration::class,
        \WpStarter\Wordpress\Bootstrap\HandleExceptions::class,
        \WpStarter\Foundation\Bootstrap\RegisterFacades::class,
        \WpStarter\Foundation\Bootstrap\SetRequestForConsole::class,
        \WpStarter\Foundation\Bootstrap\RegisterProviders::class,
        \WpStarter\Foundation\Bootstrap\BootProviders::class,
    ];

    function earlyBootstrap(){
        foreach ($this->earlyBootstrapers as $bootstraper){
            $this->app->bootstrapOne($bootstraper);
        }
    }
}