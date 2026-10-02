<?php

namespace WpStarter\Auth\Events;

use WpStarter\Queue\SerializesModels;

class PasswordReset
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user  The user.
     */
    public function __construct(
        public $user,
    ) {
    }
}
