<?php

namespace WpStarter\Tests\Integration\Foundation\Fixtures\Providers;

use WpStarter\Console\Application;
use WpStarter\Support\ServiceProvider;
use WpStarter\Tests\Integration\Foundation\Fixtures\Console\ThrowExceptionCommand;
use WpStarter\Tests\Integration\Foundation\Fixtures\Logs\ThrowExceptionLogHandler;

class ThrowUncaughtExceptionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $config = $this->app['config'];

        $config->set('logging.default', 'throw_exception');

        $config->set('logging.channels.throw_exception', [
            'driver' => 'monolog',
            'handler' => ThrowExceptionLogHandler::class,
        ]);
    }

    public function boot()
    {
        Application::starting(function ($artisan) {
            $artisan->add(new ThrowExceptionCommand);
        });
    }
}
