<?php

namespace WpStarter\Support\Facades;

use WpStarter\Http\Client\Factory;

/**
 * @method static \WpStarter\Http\Client\Factory globalMiddleware(callable $middleware)
 * @method static \WpStarter\Http\Client\Factory globalRequestMiddleware(callable $middleware)
 * @method static \WpStarter\Http\Client\Factory globalResponseMiddleware(callable $middleware)
 * @method static \WpStarter\Http\Client\Factory globalOptions(\Closure|array $options)
 * @method static mixed withoutGlobalConfiguration(\Closure $callback)
 * @method static \GuzzleHttp\Promise\PromiseInterface response(array|string|null $body = null, int $status = 200, array $headers = [])
 * @method static \GuzzleHttp\Psr7\Response psr7Response(array|string|null $body = null, int $status = 200, array $headers = [])
 * @method static \WpStarter\Http\Client\RequestException failedRequest(array|string|null $body = null, int $status = 200, array $headers = [])
 * @method static \Closure failedConnection(string|null $message = null)
 * @method static \WpStarter\Http\Client\ResponseSequence sequence(array $responses = [])
 * @method static bool preventingStrayRequests()
 * @method static \WpStarter\Http\Client\Factory allowStrayRequests(array|null $only = null)
 * @method static \WpStarter\Http\Client\Factory record()
 * @method static void recordRequestResponsePair(\WpStarter\Http\Client\Request $request, \WpStarter\Http\Client\Response|null $response)
 * @method static void assertSent(callable|\Closure $callback)
 * @method static void assertSentInOrder(array $callbacks)
 * @method static void assertNotSent(callable|\Closure $callback)
 * @method static void assertNothingSent()
 * @method static void assertSentCount(int $count)
 * @method static void assertSequencesAreEmpty()
 * @method static \WpStarter\Support\Collection recorded(\Closure|callable $callback = null)
 * @method static \WpStarter\Http\Client\PendingRequest createPendingRequest()
 * @method static \WpStarter\Contracts\Events\Dispatcher|null getDispatcher()
 * @method static array getGlobalMiddleware()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static \WpStarter\Http\Client\PendingRequest baseUrl(string $url)
 * @method static \WpStarter\Http\Client\PendingRequest withBody(\Psr\Http\Message\StreamInterface|string $content, string $contentType = 'application/json')
 * @method static \WpStarter\Http\Client\PendingRequest asJson()
 * @method static \WpStarter\Http\Client\PendingRequest asForm()
 * @method static \WpStarter\Http\Client\PendingRequest attach(string|array $name, string|resource $contents = '', string|null $filename = null, array $headers = [])
 * @method static \WpStarter\Http\Client\PendingRequest asMultipart()
 * @method static \WpStarter\Http\Client\PendingRequest bodyFormat(string $format)
 * @method static \WpStarter\Http\Client\PendingRequest withQueryParameters(array $parameters)
 * @method static \WpStarter\Http\Client\PendingRequest contentType(string $contentType)
 * @method static \WpStarter\Http\Client\PendingRequest acceptJson()
 * @method static \WpStarter\Http\Client\PendingRequest accept(string $contentType)
 * @method static \WpStarter\Http\Client\PendingRequest withHeaders(array $headers)
 * @method static \WpStarter\Http\Client\PendingRequest withHeader(string $name, mixed $value)
 * @method static \WpStarter\Http\Client\PendingRequest replaceHeaders(array $headers)
 * @method static \WpStarter\Http\Client\PendingRequest withBasicAuth(string $username, string $password)
 * @method static \WpStarter\Http\Client\PendingRequest withDigestAuth(string $username, string $password)
 * @method static \WpStarter\Http\Client\PendingRequest withNtlmAuth(string $username, string $password)
 * @method static \WpStarter\Http\Client\PendingRequest withToken(string $token, string $type = 'Bearer')
 * @method static \WpStarter\Http\Client\PendingRequest withUserAgent(string|bool $userAgent)
 * @method static \WpStarter\Http\Client\PendingRequest withUrlParameters(array $parameters = [])
 * @method static \WpStarter\Http\Client\PendingRequest withCookies(array $cookies, string $domain)
 * @method static \WpStarter\Http\Client\PendingRequest maxRedirects(int $max)
 * @method static \WpStarter\Http\Client\PendingRequest withoutRedirecting()
 * @method static \WpStarter\Http\Client\PendingRequest withoutVerifying()
 * @method static \WpStarter\Http\Client\PendingRequest sink(string|resource $to)
 * @method static \WpStarter\Http\Client\PendingRequest timeout(int|float $seconds)
 * @method static \WpStarter\Http\Client\PendingRequest connectTimeout(int|float $seconds)
 * @method static \WpStarter\Http\Client\PendingRequest retry(array|int $times, \Closure|int $sleepMilliseconds = 0, callable|null $when = null, bool $throw = true)
 * @method static \WpStarter\Http\Client\PendingRequest withOptions(array $options)
 * @method static \WpStarter\Http\Client\PendingRequest withMiddleware(callable $middleware)
 * @method static \WpStarter\Http\Client\PendingRequest withRequestMiddleware(callable $middleware)
 * @method static \WpStarter\Http\Client\PendingRequest withResponseMiddleware(callable $middleware)
 * @method static \WpStarter\Http\Client\PendingRequest withAttributes(array $attributes)
 * @method static \WpStarter\Http\Client\PendingRequest beforeSending(callable $callback)
 * @method static \WpStarter\Http\Client\PendingRequest afterResponse(callable|null $callback)
 * @method static \WpStarter\Http\Client\PendingRequest throw(callable|null $callback = null)
 * @method static \WpStarter\Http\Client\PendingRequest throwIf(callable|bool $condition)
 * @method static \WpStarter\Http\Client\PendingRequest throwUnless(callable|bool $condition)
 * @method static \WpStarter\Http\Client\PendingRequest dump()
 * @method static \WpStarter\Http\Client\PendingRequest dd()
 * @method static \WpStarter\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface get(string $url, array|string|null $query = null)
 * @method static \WpStarter\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface head(string $url, array|string|null $query = null)
 * @method static \WpStarter\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface post(string $url, array|\JsonSerializable|\WpStarter\Contracts\Support\Arrayable $data = [])
 * @method static \WpStarter\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface patch(string $url, array|\JsonSerializable|\WpStarter\Contracts\Support\Arrayable $data = [])
 * @method static \WpStarter\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface put(string $url, array|\JsonSerializable|\WpStarter\Contracts\Support\Arrayable $data = [])
 * @method static \WpStarter\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface delete(string $url, array|\JsonSerializable|\WpStarter\Contracts\Support\Arrayable $data = [])
 * @method static array pool(callable $callback, int|null $concurrency = null)
 * @method static \WpStarter\Http\Client\Batch batch(callable $callback)
 * @method static \WpStarter\Http\Client\Response|\WpStarter\Http\Client\Promises\LazyPromise send(string $method, string $url, array $options = [])
 * @method static \GuzzleHttp\Client buildClient()
 * @method static \GuzzleHttp\Client createClient(\GuzzleHttp\HandlerStack $handlerStack)
 * @method static \GuzzleHttp\HandlerStack buildHandlerStack()
 * @method static \GuzzleHttp\HandlerStack pushHandlers(\GuzzleHttp\HandlerStack $handlerStack)
 * @method static \Closure buildBeforeSendingHandler()
 * @method static \Closure buildRecorderHandler()
 * @method static \Closure buildStubHandler()
 * @method static \Psr\Http\Message\RequestInterface runBeforeSendingCallbacks(\Psr\Http\Message\RequestInterface $request, array $options)
 * @method static array mergeOptions(array ...$options)
 * @method static \WpStarter\Http\Client\PendingRequest stub(callable $callback)
 * @method static bool isAllowedRequestUrl(string $url)
 * @method static \WpStarter\Http\Client\PendingRequest async(bool $async = true)
 * @method static \GuzzleHttp\Promise\PromiseInterface|null getPromise()
 * @method static \WpStarter\Http\Client\PendingRequest truncateExceptionsAt(int $length)
 * @method static \WpStarter\Http\Client\PendingRequest dontTruncateExceptions()
 * @method static \WpStarter\Http\Client\PendingRequest setClient(\GuzzleHttp\Client $client)
 * @method static \WpStarter\Http\Client\PendingRequest setHandler(callable $handler)
 * @method static array getOptions()
 * @method static \WpStarter\Http\Client\PendingRequest|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \WpStarter\Http\Client\PendingRequest|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
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

    /**
     * Register a stub callable that will intercept requests and be able to return stub responses.
     *
     * @param  \Closure|array|null  $callback
     * @return \WpStarter\Http\Client\Factory
     */
    public static function fake($callback = null)
    {
        return ws_tap(static::getFacadeRoot(), function ($fake) use ($callback) {
            static::swap($fake->fake($callback));
        });
    }

    /**
     * Register a response sequence for the given URL pattern.
     *
     * @param  string  $urlPattern
     * @return \WpStarter\Http\Client\ResponseSequence
     */
    public static function fakeSequence(string $urlPattern = '*')
    {
        $fake = ws_tap(static::getFacadeRoot(), function ($fake) {
            static::swap($fake);
        });

        return $fake->fakeSequence($urlPattern);
    }

    /**
     * Indicate that an exception should be thrown if any request is not faked.
     *
     * @param  bool  $prevent
     * @return \WpStarter\Http\Client\Factory
     */
    public static function preventStrayRequests($prevent = true)
    {
        return ws_tap(static::getFacadeRoot(), function ($fake) use ($prevent) {
            static::swap($fake->preventStrayRequests($prevent));
        });
    }

    /**
     * Stub the given URL using the given callback.
     *
     * @param  string  $url
     * @param  \WpStarter\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface|callable  $callback
     * @return \WpStarter\Http\Client\Factory
     */
    public static function stubUrl($url, $callback)
    {
        return ws_tap(static::getFacadeRoot(), function ($fake) use ($url, $callback) {
            static::swap($fake->stubUrl($url, $callback));
        });
    }
}
