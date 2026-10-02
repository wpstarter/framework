<?php

namespace WpStarter\Http\Client;

use GuzzleHttp\Utils;

/**
 * @mixin \WpStarter\Http\Client\Factory
 */
class Pool
{
    /**
     * The factory instance.
     *
     * @var \WpStarter\Http\Client\Factory
     */
    protected $factory;

    /**
     * The handler function for the Guzzle client.
     *
     * @var callable
     */
    protected $handler;

    /**
     * The pool of requests.
     *
     * @var array<array-key, \WpStarter\Http\Client\PendingRequest>
     */
    protected $pool = [];

    /**
     * Create a new requests pool.
     *
     * @param  \WpStarter\Http\Client\Factory|null  $factory
     */
    public function __construct(?Factory $factory = null)
    {
        $this->factory = $factory ?: new Factory();
        $this->handler = Utils::chooseHandler();
    }

    /**
     * Add a request to the pool with a numeric index.
     *
     * @return \WpStarter\Http\Client\PendingRequest|\GuzzleHttp\Promise\Promise
     */
    public function newRequest()
    {
        return $this->pool[] = $this->asyncRequest();
    }

    /**
     * Add a request to the pool with a key.
     *
     * @param  string  $key
     * @return \WpStarter\Http\Client\PendingRequest
     */
    public function as(string $key)
    {
        return $this->pool[$key] = $this->asyncRequest();
    }

    /**
     * Retrieve a new async pending request.
     *
     * @return \WpStarter\Http\Client\PendingRequest
     */
    protected function asyncRequest()
    {
        return $this->factory->setHandler($this->handler)->async();
    }

    /**
     * Retrieve the requests in the pool.
     *
     * @return array<array-key, \WpStarter\Http\Client\PendingRequest>
     */
    public function getRequests()
    {
        return $this->pool;
    }

    /**
     * Add a request to the pool with a numeric index and forward the method call to the request.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \WpStarter\Http\Client\PendingRequest|\GuzzleHttp\Promise\Promise
     */
    public function __call($method, $parameters)
    {
        return $this->newRequest()->{$method}(...$parameters);
    }
}
