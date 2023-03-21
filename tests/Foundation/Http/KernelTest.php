<?php

namespace WpStarter\Tests\Foundation\Bootstrap\Http;

use WpStarter\Events\Dispatcher;
use WpStarter\Foundation\Application;
use WpStarter\Foundation\Http\Kernel;
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
            \WpStarter\Cookie\Middleware\EncryptCookies::class,
            \WpStarter\Session\Middleware\StartSession::class,
            \WpStarter\View\Middleware\ShareErrorsFromSession::class,
            \WpStarter\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \WpStarter\Routing\Middleware\ThrottleRequests::class,
            \WpStarter\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \WpStarter\Session\Middleware\AuthenticateSession::class,
            \WpStarter\Routing\Middleware\SubstituteBindings::class,
            \WpStarter\Auth\Middleware\Authorize::class,
        ], $kernel->getMiddlewarePriority());
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
