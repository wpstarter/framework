<?php

namespace WpStarter\Tests\Foundation\Http\Middleware;

use WpStarter\Http\Exceptions\MalformedUrlException;
use WpStarter\Http\Middleware\ValidatePathEncoding;
use WpStarter\Http\Request;
use WpStarter\Http\Response;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class ValidatePathEncodingTest extends TestCase
{
    #[TestWith(['/'])]
    #[TestWith(['valid-path'])]
    #[TestWith(['ä'])]
    #[TestWith(['with%20space'])]
    #[TestWith(['%E6%B1%89%E5%AD%97%E5%AD%97%E7%AC%A6%E9%9B%86'])]
    public function testValidPathsArePassing(string $path): void
    {
        $middleware = new ValidatePathEncoding;
        $symfonyRequest = new SymfonyRequest;
        $symfonyRequest->server->set('REQUEST_METHOD', 'GET');
        $symfonyRequest->server->set('REQUEST_URI', $path);
        $request = Request::createFromBase($symfonyRequest);

        $response = $middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame(200, $response->status());
        $this->assertSame('OK', $response->content());
    }

    #[TestWith(['%C0'])]
    #[TestWith(['%c0'])]
    public function testInvalidPathsAreFailing(string $path): void
    {
        $middleware = new ValidatePathEncoding;
        $symfonyRequest = new SymfonyRequest;
        $symfonyRequest->server->set('REQUEST_METHOD', 'GET');
        $symfonyRequest->server->set('REQUEST_URI', $path);
        $request = Request::createFromBase($symfonyRequest);

        try {
            $middleware->handle($request, fn () => new Response('OK'));

            $this->fail('MalformedUrlExceptions should have been thrown.');
        } catch(MalformedUrlException $e) {
            $this->assertSame(400, $e->getStatusCode());
            $this->assertSame('Malformed URL.', $e->getMessage());
        }
    }
}
