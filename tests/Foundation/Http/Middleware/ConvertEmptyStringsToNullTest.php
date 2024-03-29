<?php

namespace WpStarter\Tests\Foundation\Bootstrap\Http\Middleware;

use WpStarter\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use WpStarter\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class ConvertEmptyStringsToNullTest extends TestCase
{
    public function testConvertsEmptyStringsToNull()
    {
        $middleware = new ConvertEmptyStringsToNull;
        $symfonyRequest = new SymfonyRequest([
            'foo' => 'bar',
            'baz' => '',
        ]);
        $symfonyRequest->server->set('REQUEST_METHOD', 'GET');
        $request = Request::createFromBase($symfonyRequest);

        $middleware->handle($request, function (Request $request) {
            $this->assertSame('bar', $request->get('foo'));
            $this->assertNull($request->get('bar'));
        });
    }
}
