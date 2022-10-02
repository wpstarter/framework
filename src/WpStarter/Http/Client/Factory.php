<?php

namespace WpStarter\Http\Client;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as Psr7Response;
use GuzzleHttp\TransferStats;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Support\Str;
use WpStarter\Support\Traits\Macroable;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * @method \WpStarter\Http\Client\PendingRequest accept(string $contentType)
 * @method \WpStarter\Http\Client\PendingRequest acceptJson()
 * @method \WpStarter\Http\Client\PendingRequest asForm()
 * @method \WpStarter\Http\Client\PendingRequest asJson()
 * @method \WpStarter\Http\Client\PendingRequest asMultipart()
 * @method \WpStarter\Http\Client\PendingRequest async()
 * @method \WpStarter\Http\Client\PendingRequest attach(string|array $name, string|resource $contents = '', string|null $filename = null, array $headers = [])
 * @method \WpStarter\Http\Client\PendingRequest baseUrl(string $url)
 * @method \WpStarter\Http\Client\PendingRequest beforeSending(callable $callback)
 * @method \WpStarter\Http\Client\PendingRequest bodyFormat(string $format)
 * @method \WpStarter\Http\Client\PendingRequest contentType(string $contentType)
 * @method \WpStarter\Http\Client\PendingRequest dd()
 * @method \WpStarter\Http\Client\PendingRequest dump()
 * @method \WpStarter\Http\Client\PendingRequest retry(int $times, int $sleep = 0, ?callable $when = null)
 * @method \WpStarter\Http\Client\PendingRequest sink(string|resource $to)
 * @method \WpStarter\Http\Client\PendingRequest stub(callable $callback)
 * @method \WpStarter\Http\Client\PendingRequest timeout(int $seconds)
 * @method \WpStarter\Http\Client\PendingRequest withBasicAuth(string $username, string $password)
 * @method \WpStarter\Http\Client\PendingRequest withBody(resource|string $content, string $contentType)
 * @method \WpStarter\Http\Client\PendingRequest withCookies(array $cookies, string $domain)
 * @method \WpStarter\Http\Client\PendingRequest withDigestAuth(string $username, string $password)
 * @method \WpStarter\Http\Client\PendingRequest withHeaders(array $headers)
 * @method \WpStarter\Http\Client\PendingRequest withMiddleware(callable $middleware)
 * @method \WpStarter\Http\Client\PendingRequest withOptions(array $options)
 * @method \WpStarter\Http\Client\PendingRequest withToken(string $token, string $type = 'Bearer')
 * @method \WpStarter\Http\Client\PendingRequest withUserAgent(string $userAgent)
 * @method \WpStarter\Http\Client\PendingRequest withoutRedirecting()
 * @method \WpStarter\Http\Client\PendingRequest withoutVerifying()
 * @method array pool(callable $callback)
 * @method \WpStarter\Http\Client\Response delete(string $url, array $data = [])
 * @method \WpStarter\Http\Client\Response get(string $url, array|string|null $query = null)
 * @method \WpStarter\Http\Client\Response head(string $url, array|string|null $query = null)
 * @method \WpStarter\Http\Client\Response patch(string $url, array $data = [])
 * @method \WpStarter\Http\Client\Response post(string $url, array $data = [])
 * @method \WpStarter\Http\Client\Response put(string $url, array $data = [])
 * @method \WpStarter\Http\Client\Response send(string $method, string $url, array $options = [])
 *
 * @see \WpStarter\Http\Client\PendingRequest
 */
class Factory
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The event dispatcher implementation.
     *
     * @var \WpStarter\Contracts\Events\Dispatcher|null
     */
    protected $dispatcher;

    /**
     * The stub callables that will handle requests.
     *
     * @var \WpStarter\Support\Collection
     */
    protected $stubCallbacks;

    /**
     * Indicates if the factory is recording requests and responses.
     *
     * @var bool
     */
    protected $recording = false;

    /**
     * The recorded response array.
     *
     * @var array
     */
    protected $recorded = [];

    /**
     * All created response sequences.
     *
     * @var array
     */
    protected $responseSequences = [];

    /**
     * Create a new factory instance.
     *
     * @param  \WpStarter\Contracts\Events\Dispatcher|null  $dispatcher
     * @return void
     */
    public function __construct(Dispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;

        $this->stubCallbacks = ws_collect();
    }

    /**
     * Create a new response instance for use during stubbing.
     *
     * @param  array|string  $body
     * @param  int  $status
     * @param  array  $headers
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public static function response($body = null, $status = 200, $headers = [])
    {
        if (is_array($body)) {
            $body = json_encode($body);

            $headers['Content-Type'] = 'application/json';
        }

        $response = new Psr7Response($status, $headers, $body);

        return class_exists(\GuzzleHttp\Promise\Create::class)
            ? \GuzzleHttp\Promise\Create::promiseFor($response)
            : \GuzzleHttp\Promise\promise_for($response);
    }

    /**
     * Get an invokable object that returns a sequence of responses in order for use during stubbing.
     *
     * @param  array  $responses
     * @return \WpStarter\Http\Client\ResponseSequence
     */
    public function sequence(array $responses = [])
    {
        return $this->responseSequences[] = new ResponseSequence($responses);
    }

    /**
     * Register a stub callable that will intercept requests and be able to return stub responses.
     *
     * @param  callable|array  $callback
     * @return $this
     */
    public function fake($callback = null)
    {
        $this->record();

        $this->recorded = [];

        if (is_null($callback)) {
            $callback = function () {
                return static::response();
            };
        }

        if (is_array($callback)) {
            foreach ($callback as $url => $callable) {
                $this->stubUrl($url, $callable);
            }

            return $this;
        }

        $this->stubCallbacks = $this->stubCallbacks->merge(ws_collect([
            function ($request, $options) use ($callback) {
                $response = $callback instanceof Closure
                                ? $callback($request, $options)
                                : $callback;

                if ($response instanceof PromiseInterface) {
                    $options['on_stats'](new TransferStats(
                        $request->toPsrRequest(),
                        $response->wait(),
                    ));
                }

                return $response;
            },
        ]));

        return $this;
    }

    /**
     * Register a response sequence for the given URL pattern.
     *
     * @param  string  $url
     * @return \WpStarter\Http\Client\ResponseSequence
     */
    public function fakeSequence($url = '*')
    {
        return ws_tap($this->sequence(), function ($sequence) use ($url) {
            $this->fake([$url => $sequence]);
        });
    }

    /**
     * Stub the given URL using the given callback.
     *
     * @param  string  $url
     * @param  \WpStarter\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface|callable  $callback
     * @return $this
     */
    public function stubUrl($url, $callback)
    {
        return $this->fake(function ($request, $options) use ($url, $callback) {
            if (! Str::is(Str::start($url, '*'), $request->url())) {
                return;
            }

            return $callback instanceof Closure || $callback instanceof ResponseSequence
                        ? $callback($request, $options)
                        : $callback;
        });
    }

    /**
     * Begin recording request / response pairs.
     *
     * @return $this
     */
    protected function record()
    {
        $this->recording = true;

        return $this;
    }

    /**
     * Record a request response pair.
     *
     * @param  \WpStarter\Http\Client\Request  $request
     * @param  \WpStarter\Http\Client\Response  $response
     * @return void
     */
    public function recordRequestResponsePair($request, $response)
    {
        if ($this->recording) {
            $this->recorded[] = [$request, $response];
        }
    }

    /**
     * Assert that a request / response pair was recorded matching a given truth test.
     *
     * @param  callable  $callback
     * @return void
     */
    public function assertSent($callback)
    {
        PHPUnit::assertTrue(
            $this->recorded($callback)->count() > 0,
            'An expected request was not recorded.'
        );
    }

    /**
     * Assert that the given request was sent in the given order.
     *
     * @param  array  $callbacks
     * @return void
     */
    public function assertSentInOrder($callbacks)
    {
        $this->assertSentCount(count($callbacks));

        foreach ($callbacks as $index => $url) {
            $callback = is_callable($url) ? $url : function ($request) use ($url) {
                return $request->url() == $url;
            };

            PHPUnit::assertTrue($callback(
                $this->recorded[$index][0],
                $this->recorded[$index][1]
            ), 'An expected request (#'.($index + 1).') was not recorded.');
        }
    }

    /**
     * Assert that a request / response pair was not recorded matching a given truth test.
     *
     * @param  callable  $callback
     * @return void
     */
    public function assertNotSent($callback)
    {
        PHPUnit::assertFalse(
            $this->recorded($callback)->count() > 0,
            'Unexpected request was recorded.'
        );
    }

    /**
     * Assert that no request / response pair was recorded.
     *
     * @return void
     */
    public function assertNothingSent()
    {
        PHPUnit::assertEmpty(
            $this->recorded,
            'Requests were recorded.'
        );
    }

    /**
     * Assert how many requests have been recorded.
     *
     * @param  int  $count
     * @return void
     */
    public function assertSentCount($count)
    {
        PHPUnit::assertCount($count, $this->recorded);
    }

    /**
     * Assert that every created response sequence is empty.
     *
     * @return void
     */
    public function assertSequencesAreEmpty()
    {
        foreach ($this->responseSequences as $responseSequence) {
            PHPUnit::assertTrue(
                $responseSequence->isEmpty(),
                'Not all response sequences are empty.'
            );
        }
    }

    /**
     * Get a collection of the request / response pairs matching the given truth test.
     *
     * @param  callable  $callback
     * @return \WpStarter\Support\Collection
     */
    public function recorded($callback = null)
    {
        if (empty($this->recorded)) {
            return ws_collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return ws_collect($this->recorded)->filter(function ($pair) use ($callback) {
            return $callback($pair[0], $pair[1]);
        });
    }

    /**
     * Create a new pending request instance for this factory.
     *
     * @return \WpStarter\Http\Client\PendingRequest
     */
    protected function newPendingRequest()
    {
        return new PendingRequest($this);
    }

    /**
     * Get the current event dispatcher implementation.
     *
     * @return \WpStarter\Contracts\Events\Dispatcher|null
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Execute a method against a new pending request instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return ws_tap($this->newPendingRequest(), function ($request) {
            $request->stub($this->stubCallbacks);
        })->{$method}(...$parameters);
    }
}
