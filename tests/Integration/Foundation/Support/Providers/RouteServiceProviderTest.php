<?php

namespace WpStarter\Tests\Integration\Foundation\Support\Providers;

use WpStarter\Foundation\Application;
use WpStarter\Foundation\Configuration\Exceptions;
use WpStarter\Foundation\Configuration\Middleware;
use WpStarter\Foundation\Support\Providers\RouteServiceProvider;
use WpStarter\Support\Facades\Route;
use WpStarter\Testing\Assert;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;

#[WithConfig('filesystems.disks.local.serve', false)]
class RouteServiceProviderTest extends TestCase
{
    /**
     * Resolve application implementation.
     *
     * @return \WpStarter\Foundation\Application
     */
    protected function resolveApplication()
    {
        return Application::configure(static::applicationBasePath())
            ->withProviders([
                AppRouteServiceProvider::class,
            ])
            ->withRouting(
                using: function () {
                    Route::get('login', fn () => 'Login')->name('login');
                }
            )
            ->withMiddleware(function (Middleware $middleware) {
                //
            })
            ->withExceptions(function (Exceptions $exceptions) {
                //
            })->create();
    }

    public function test_it_can_register_multiple_route_service_providers()
    {
        Assert::assertArraySubset([
            RouteServiceProvider::class => true,
            AppRouteServiceProvider::class => true,
        ], $this->app->getLoadedProviders());
    }

    public function test_it_can_uses_routes_registered_using_bootstrap_file()
    {
        $this->get(ws_route('login'))
            ->assertOk()
            ->assertSee('Login');
    }

    public function test_it_can_uses_routes_registered_using_configuration_file()
    {
        $this->get(ws_route('dashboard'))
            ->assertOk()
            ->assertSee('Hello');
    }
}

class AppRouteServiceProvider extends RouteServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            Route::get('dashboard', fn () => 'Hello')->name('dashboard');
        });
    }
}
