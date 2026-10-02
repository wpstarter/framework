<?php

namespace WpStarter\Http\Client\Events;

use WpStarter\Http\Client\Request;

class RequestSending
{
    /**
     * The request instance.
     *
     * @var \WpStarter\Http\Client\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Http\Client\Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
