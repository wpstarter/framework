<?php

namespace Auth;

use WpStarter\Auth\Middleware\RedirectIfAuthenticated;
use PHPUnit\Framework\TestCase;

class RedirectIfAuthenticatedMiddlewareTest extends TestCase
{
    public function testItCanGenerateDefinitionViaStaticMethod()
    {
        $signature = RedirectIfAuthenticated::using('foo');
        $this->assertSame('WpStarter\Auth\Middleware\RedirectIfAuthenticated:foo', $signature);

        $signature = RedirectIfAuthenticated::using('foo', 'bar');
        $this->assertSame('WpStarter\Auth\Middleware\RedirectIfAuthenticated:foo,bar', $signature);

        $signature = RedirectIfAuthenticated::using('foo', 'bar', 'baz');
        $this->assertSame('WpStarter\Auth\Middleware\RedirectIfAuthenticated:foo,bar,baz', $signature);
    }
}
