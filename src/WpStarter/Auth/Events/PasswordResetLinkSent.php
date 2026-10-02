<?php

namespace WpStarter\Auth\Events;

use WpStarter\Queue\SerializesModels;

class PasswordResetLinkSent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Contracts\Auth\CanResetPassword  $user  The user instance.
     */
    public function __construct(
        public $user,
    ) {
    }
}
