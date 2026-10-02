<?php

namespace WpStarter\Auth\Events;

use WpStarter\Queue\SerializesModels;

class OtherDeviceLogout
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $guard  The authentication guard name.
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user  \WpStarter\Contracts\Auth\Authenticatable
     */
    public function __construct(
        public $guard,
        public $user,
    ) {
    }
}
