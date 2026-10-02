<?php

namespace WpStarter\Auth\Events;

use WpStarter\Queue\SerializesModels;

class Registered
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user  The authenticated user.
     */
    public function __construct(
        public $user,
    ) {
    }
}
