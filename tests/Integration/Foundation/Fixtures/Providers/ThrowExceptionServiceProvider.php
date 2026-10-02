<?php

namespace WpStarter\Tests\Integration\Foundation\Fixtures\Providers;

use WpStarter\Console\Application;
use WpStarter\Support\ServiceProvider;
use WpStarter\Tests\Integration\Foundation\Fixtures\Console\ThrowExceptionCommand;

class ThrowExceptionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Application::starting(function ($artisan) {
            $artisan->add(new ThrowExceptionCommand);
        });
    }
}
