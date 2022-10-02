<?php

namespace WpStarter\Foundation\Http\Events;

class RequestHandled
{
    /**
     * The request instance.
     *
     * @var \WpStarter\Http\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \WpStarter\Http\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \WpStarter\Http\Response  $response
     * @return void
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
