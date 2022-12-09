<?php

namespace WpStarter\Auth\Events;

use WpStarter\Http\Request;

class Lockout
{
    /**
     * The throttled request.
     *
     * @var \WpStarter\Http\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
