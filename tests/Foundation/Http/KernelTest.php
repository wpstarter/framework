<?php

namespace WpStarter\Tests\Foundation\Http;

use WpStarter\Events\Dispatcher;
use WpStarter\Foundation\Application;
use WpStarter\Foundation\Events\Terminating;
use WpStarter\Foundation\Http\Kernel;
use WpStarter\Http\Request;
use WpStarter\Http\Response;
use WpStarter\Routing\Router;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    public function testGetMiddlewareGroups()
    {
        $kernel = new Kernel($this->getApplication(), $this->getRouter());

        $this->assertEquals([], $kernel->getMiddlewareGroups());
    }

    public function testGetRouteMiddleware()
    {
        $kernel = new Kernel($this->getApplication(), $this->getRouter());

        $this->assertEquals([], $kernel->getRouteMiddleware());
    }

    public function testGetMiddlewarePriority()
    {
        $kernel = new Kernel($this->getApplication(), $this->getRouter());

        $this->assertEquals([
            \WpStarter\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \WpStarter\Cookie\Middleware\EncryptCookies::class,
            \WpStarter\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \WpStarter\Session\Middleware\StartSession::class,
            \WpStarter\View\Middleware\ShareErrorsFromSession::class,
            \WpStarter\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \WpStarter\Routing\Middleware\ThrottleRequests::class,
            \WpStarter\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \WpStarter\Contracts\Session\Middleware\AuthenticatesSessions::class,
            \WpStarter\Routing\Middleware\SubstituteBindings::class,
            \WpStarter\Auth\Middleware\Authorize::class,
        ], $kernel->getMiddlewarePriority());
    }

    public function testAddToMiddlewarePriorityAfter()
    {
        $kernel = new Kernel($this->getApplication(), $this->getRouter());

        $kernel->addToMiddlewarePriorityAfter(
            [
                \WpStarter\Cookie\Middleware\EncryptCookies::class,
                \WpStarter\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            ],
            \WpStarter\Routing\Middleware\ValidateSignature::class,
        );

        $this->assertEquals([
            \WpStarter\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \WpStarter\Cookie\Middleware\EncryptCookies::class,
            \WpStarter\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \WpStarter\Session\Middleware\StartSession::class,
            \WpStarter\View\Middleware\ShareErrorsFromSession::class,
            \WpStarter\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \WpStarter\Routing\Middleware\ValidateSignature::class,
            \WpStarter\Routing\Middleware\ThrottleRequests::class,
            \WpStarter\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \WpStarter\Contracts\Session\Middleware\AuthenticatesSessions::class,
            \WpStarter\Routing\Middleware\SubstituteBindings::class,
            \WpStarter\Auth\Middleware\Authorize::class,
        ], $kernel->getMiddlewarePriority());
    }

    public function testAddToMiddlewarePriorityBefore()
    {
        $kernel = new Kernel($this->getApplication(), $this->getRouter());

        $kernel->addToMiddlewarePriorityBefore(
            [
                \WpStarter\Cookie\Middleware\EncryptCookies::class,
                \WpStarter\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            ],
            \WpStarter\Routing\Middleware\ValidateSignature::class,
        );

        $this->assertEquals([
            \WpStarter\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \WpStarter\Routing\Middleware\ValidateSignature::class,
            \WpStarter\Cookie\Middleware\EncryptCookies::class,
            \WpStarter\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \WpStarter\Session\Middleware\StartSession::class,
            \WpStarter\View\Middleware\ShareErrorsFromSession::class,
            \WpStarter\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \WpStarter\Routing\Middleware\ThrottleRequests::class,
            \WpStarter\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \WpStarter\Contracts\Session\Middleware\AuthenticatesSessions::class,
            \WpStarter\Routing\Middleware\SubstituteBindings::class,
            \WpStarter\Auth\Middleware\Authorize::class,
        ], $kernel->getMiddlewarePriority());
    }

    public function testItTriggersTerminatingEvent()
    {
        $called = [];
        $app = $this->getApplication();
        $events = new Dispatcher($app);
        $app->instance('events', $events);
        $kernel = new Kernel($app, $this->getRouter());
        $app->instance('terminating-middleware', new class($called)
        {
            public function __construct(private &$called)
            {
                //
            }

            public function handle($request, $next)
            {
                return $next($request);
            }

            public function terminate($request, $response)
            {
                $this->called[] = 'terminating middleware';
            }
        });
        $kernel->setGlobalMiddleware([
            'terminating-middleware',
        ]);
        $events->listen(function (Terminating $terminating) use (&$called) {
            $called[] = 'terminating event';
        });
        $app->terminating(function () use (&$called) {
            $called[] = 'terminating callback';
        });

        $kernel->terminate(new Request(), new Response());

        $this->assertSame([
            'terminating event',
            'terminating middleware',
            'terminating callback',
        ], $called);
    }

    /**
     * @return \WpStarter\Contracts\Foundation\Application
     */
    protected function getApplication()
    {
        return new Application;
    }

    /**
     * @return \WpStarter\Routing\Router
     */
    protected function getRouter()
    {
        return new Router(new Dispatcher);
    }
}
