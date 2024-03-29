<?php

namespace WpStarter\Tests\Cookie\Middleware;

use WpStarter\Container\Container;
use WpStarter\Contracts\Encryption\Encrypter as EncrypterContract;
use WpStarter\Cookie\CookieJar;
use WpStarter\Cookie\Middleware\AddQueuedCookiesToResponse;
use WpStarter\Cookie\Middleware\EncryptCookies;
use WpStarter\Encryption\Encrypter;
use WpStarter\Events\Dispatcher;
use WpStarter\Http\Request;
use WpStarter\Http\Response;
use WpStarter\Routing\Controller;
use WpStarter\Routing\Router;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;

class EncryptCookiesTest extends TestCase
{
    /**
     * @var \WpStarter\Routing\Router
     */
    protected $router;

    protected $setCookiePath = 'cookie/set';
    protected $queueCookiePath = 'cookie/queue';

    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container;
        $container->singleton(EncrypterContract::class, function () {
            return new Encrypter(str_repeat('a', 16));
        });

        $this->router = new Router(new Dispatcher, $container);
    }

    public function testSetCookieEncryption()
    {
        $this->router->get($this->setCookiePath, [
            'middleware' => EncryptCookiesTestMiddleware::class,
            'uses' => EncryptCookiesTestController::class.'@setCookies',
        ]);

        $response = $this->router->dispatch(Request::create($this->setCookiePath, 'GET'));

        $cookies = $response->headers->getCookies();
        $this->assertCount(2, $cookies);
        $this->assertSame('encrypted_cookie', $cookies[0]->getName());
        $this->assertNotSame('value', $cookies[0]->getValue());
        $this->assertSame('unencrypted_cookie', $cookies[1]->getName());
        $this->assertSame('value', $cookies[1]->getValue());
    }

    public function testQueuedCookieEncryption()
    {
        $this->router->get($this->queueCookiePath, [
            'middleware' => [EncryptCookiesTestMiddleware::class, AddQueuedCookiesToResponseTestMiddleware::class],
            'uses' => EncryptCookiesTestController::class.'@queueCookies',
        ]);

        $response = $this->router->dispatch(Request::create($this->queueCookiePath, 'GET'));

        $cookies = $response->headers->getCookies();
        $this->assertCount(2, $cookies);
        $this->assertSame('encrypted_cookie', $cookies[0]->getName());
        $this->assertNotSame('value', $cookies[0]->getValue());
        $this->assertSame('unencrypted_cookie', $cookies[1]->getName());
        $this->assertSame('value', $cookies[1]->getValue());
    }
}

class EncryptCookiesTestController extends Controller
{
    public function setCookies()
    {
        $response = new Response;
        $response->headers->setCookie(new Cookie('encrypted_cookie', 'value'));
        $response->headers->setCookie(new Cookie('unencrypted_cookie', 'value'));

        return $response;
    }

    public function queueCookies()
    {
        return new Response;
    }
}

class EncryptCookiesTestMiddleware extends EncryptCookies
{
    protected $except = [
        'unencrypted_cookie',
    ];
}

class AddQueuedCookiesToResponseTestMiddleware extends AddQueuedCookiesToResponse
{
    public function __construct()
    {
        $cookie = new CookieJar;
        $cookie->queue(new Cookie('encrypted_cookie', 'value'));
        $cookie->queue(new Cookie('unencrypted_cookie', 'value'));

        $this->cookies = $cookie;
    }
}
