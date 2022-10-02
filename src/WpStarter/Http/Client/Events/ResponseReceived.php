<?php

namespace WpStarter\Http\Client\Events;

use WpStarter\Http\Client\Request;
use WpStarter\Http\Client\Response;

class ResponseReceived
{
    /**
     * The request instance.
     *
     * @var \WpStarter\Http\Client\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \WpStarter\Http\Client\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Http\Client\Request  $request
     * @param  \WpStarter\Http\Client\Response  $response
     * @return void
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
