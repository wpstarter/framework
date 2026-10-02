<?php

namespace WpStarter\Routing\Events;

class Routing
{
    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Http\Request  $request  The request instance.
     */
    public function __construct(
        public $request,
    ) {
    }
}
