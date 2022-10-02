<?php

namespace WpStarter\Http\Client\Events;

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
     * Create a new event instance.
     *
     * @param  \WpStarter\Http\Client\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
