<?php

namespace WpStarter\Wordpress\Console;

use WpStarter\Foundation\Console\Kernel as ConsoleKernel;
use WpStarter\Wordpress\Bootstrap\HasEarlyBootstrappers;

class Kernel extends ConsoleKernel
{
    use HasEarlyBootstrappers;
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
}
