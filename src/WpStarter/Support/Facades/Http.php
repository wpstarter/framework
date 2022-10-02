<?php

namespace WpStarter\Support\Facades;

use WpStarter\Http\Client\Factory;

/**
 * @method static \GuzzleHttp\Promise\PromiseInterface response($body = null, $status = 200, $headers = [])
 * @method static \WpStarter\Http\Client\Factory fake($callback = null)
 * @method static \WpStarter\Http\Client\PendingRequest accept(string $contentType)
 * @method static \WpStarter\Http\Client\PendingRequest acceptJson()
 * @method static \WpStarter\Http\Client\PendingRequest asForm()
 * @method static \WpStarter\Http\Client\PendingRequest asJson()
 * @method static \WpStarter\Http\Client\PendingRequest asMultipart()
 * @method static \WpStarter\Http\Client\PendingRequest async()
 * @method static \WpStarter\Http\Client\PendingRequest attach(string|array $name, string $contents = '', string|null $filename = null, array $headers = [])
 * @method static \WpStarter\Http\Client\PendingRequest baseUrl(string $url)
 * @method static \WpStarter\Http\Client\PendingRequest beforeSending(callable $callback)
 * @method static \WpStarter\Http\Client\PendingRequest bodyFormat(string $format)
 * @method static \WpStarter\Http\Client\PendingRequest contentType(string $contentType)
 * @method static \WpStarter\Http\Client\PendingRequest dd()
 * @method static \WpStarter\Http\Client\PendingRequest dump()
 * @method static \WpStarter\Http\Client\PendingRequest retry(int $times, int $sleep = 0, ?callable $when = null)
 * @method static \WpStarter\Http\Client\PendingRequest sink(string|resource $to)
 * @method static \WpStarter\Http\Client\PendingRequest stub(callable $callback)
 * @method static \WpStarter\Http\Client\PendingRequest timeout(int $seconds)
 * @method static \WpStarter\Http\Client\PendingRequest withBasicAuth(string $username, string $password)
 * @method static \WpStarter\Http\Client\PendingRequest withBody(resource|string $content, string $contentType)
 * @method static \WpStarter\Http\Client\PendingRequest withCookies(array $cookies, string $domain)
 * @method static \WpStarter\Http\Client\PendingRequest withDigestAuth(string $username, string $password)
 * @method static \WpStarter\Http\Client\PendingRequest withHeaders(array $headers)
 * @method static \WpStarter\Http\Client\PendingRequest withMiddleware(callable $middleware)
 * @method static \WpStarter\Http\Client\PendingRequest withOptions(array $options)
 * @method static \WpStarter\Http\Client\PendingRequest withToken(string $token, string $type = 'Bearer')
 * @method static \WpStarter\Http\Client\PendingRequest withUserAgent(string $userAgent)
 * @method static \WpStarter\Http\Client\PendingRequest withoutRedirecting()
 * @method static \WpStarter\Http\Client\PendingRequest withoutVerifying()
 * @method static array pool(callable $callback)
 * @method static \WpStarter\Http\Client\Response delete(string $url, array $data = [])
 * @method static \WpStarter\Http\Client\Response get(string $url, array|string|null $query = null)
 * @method static \WpStarter\Http\Client\Response head(string $url, array|string|null $query = null)
 * @method static \WpStarter\Http\Client\Response patch(string $url, array $data = [])
 * @method static \WpStarter\Http\Client\Response post(string $url, array $data = [])
 * @method static \WpStarter\Http\Client\Response put(string $url, array $data = [])
 * @method static \WpStarter\Http\Client\Response send(string $method, string $url, array $options = [])
 * @method static \WpStarter\Http\Client\ResponseSequence fakeSequence(string $urlPattern = '*')
 * @method static void assertSent(callable $callback)
 * @method static void assertSentInOrder(array $callbacks)
 * @method static void assertNotSent(callable $callback)
 * @method static void assertNothingSent()
 * @method static void assertSentCount(int $count)
 * @method static void assertSequencesAreEmpty()
 *
 * @see \WpStarter\Http\Client\Factory
 */
class Http extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
