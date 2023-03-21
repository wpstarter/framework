<?php

namespace WpStarter\Tests\Integration\Session;

use WpStarter\Contracts\Debug\ExceptionHandler;
use WpStarter\Http\Response;
use WpStarter\Session\NullSessionHandler;
use WpStarter\Session\TokenMismatchException;
use WpStarter\Support\Facades\Route;
use WpStarter\Support\Facades\Session;
use WpStarter\Support\Str;
use Mockery;
use Orchestra\Testbench\TestCase;

class SessionPersistenceTest extends TestCase
{
    public function testSessionIsPersistedEvenIfExceptionIsThrownFromRoute()
    {
        $handler = new FakeNullSessionHandler;
        $this->assertFalse($handler->written);

        Session::extend('fake-null', function () use ($handler) {
            return $handler;
        });

        Route::get('/', function () {
            throw new TokenMismatchException;
        })->middleware('web');

        $this->get('/');
        $this->assertTrue($handler->written);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->instance(
            ExceptionHandler::class,
            $handler = Mockery::mock(ExceptionHandler::class)->shouldIgnoreMissing()
        );

        $handler->shouldReceive('render')->andReturn(new Response);

        $app['config']->set('app.key', Str::random(32));
        $app['config']->set('session.driver', 'fake-null');
        $app['config']->set('session.expire_on_close', true);
    }
}

class FakeNullSessionHandler extends NullSessionHandler
{
    public $written = false;

    public function write($sessionId, $data)
    {
        $this->written = true;

        return true;
    }
}
