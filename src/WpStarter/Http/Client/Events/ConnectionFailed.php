<?php

namespace WpStarter\Http\Client\Events;

use WpStarter\Http\Client\ConnectionException;
use WpStarter\Http\Client\Request;

class ConnectionFailed
{
    /**
     * The request instance.
     *
     * @var \WpStarter\Http\Client\Request
     */
    public $request;

    /**
     * The exception instance.
     *
     * @var \WpStarter\Http\Client\ConnectionException
     */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Http\Client\Request  $request
     * @param  \WpStarter\Http\Client\ConnectionException  $exception
     */
    public function __construct(Request $request, ConnectionException $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }
}
