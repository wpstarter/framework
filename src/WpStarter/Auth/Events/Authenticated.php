<?php

namespace WpStarter\Auth\Events;

use WpStarter\Queue\SerializesModels;

class Authenticated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $guard  The authentication guard name.
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user  The authenticated user.
     */
    public function __construct(
        public $guard,
        public $user,
    ) {
    }
}
