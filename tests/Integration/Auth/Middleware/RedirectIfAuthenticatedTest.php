<?php

namespace WpStarter\Tests\Integration\Auth\Middleware;

use WpStarter\Auth\Middleware\RedirectIfAuthenticated;
use WpStarter\Contracts\Routing\Registrar;
use WpStarter\Support\Str;
use WpStarter\Tests\Integration\Auth\Fixtures\AuthenticationTestUser;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\TestCase;

class RedirectIfAuthenticatedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        /** @var \WpStarter\Contracts\Routing\Registrar $router */
        $this->router = $this->app->make(Registrar::class);

        $this->router->get('/login', function () {
            return ws_response('Login Form');
        })->middleware(RedirectIfAuthenticated::class);

        UserFactory::new()->create();

        $user = AuthenticationTestUser::first();
        $this->router->get('/login', function () {
            return ws_response('Login Form');
        })->middleware(RedirectIfAuthenticated::class);

        UserFactory::new()->create();

        $this->user = AuthenticationTestUser::first();
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('app.key', Str::random(32));
        $app['config']->set('auth.providers.users.model', AuthenticationTestUser::class);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
    }

    public function testWhenDashboardNamedRouteIsAvailable()
    {
        $this->router->get('/named-dashboard', function () {
            return ws_response('Named Dashboard');
        })->name('dashboard');

        $response = $this->actingAs($this->user)->get('/login');

        $response->assertRedirect('/named-dashboard');
    }

    public function testWhenHomeNamedRouteIsAvailable()
    {
        $this->router->get('/named-home', function () {
            return ws_response('Named Home');
        })->name('home');

        $response = $this->actingAs($this->user)->get('/login');

        $response->assertRedirect('/named-home');
    }

    public function testWhenDashboardSlugIsAvailable()
    {
        $this->router->get('/dashboard', function () {
            return ws_response('My Dashboard');
        });

        $response = $this->actingAs($this->user)->get('/login');

        $response->assertRedirect('/dashboard');
    }

    public function testWhenHomeSlugIsAvailable()
    {
        $this->router->get('/home', function () {
            return ws_response('My Home');
        })->name('home');

        $response = $this->actingAs($this->user)->get('/login');

        $response->assertRedirect('/home');
    }

    public function testWhenHomeOrDashboardAreNotAvailable()
    {
        $response = $this->actingAs($this->user)->get('/login');

        $response->assertRedirect('/');
    }

    public function testWhenGuest()
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Login Form');
    }
}
