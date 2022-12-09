<?php

namespace WpStarter\Auth\Events;

use WpStarter\Queue\SerializesModels;

class Verified
{
    use SerializesModels;

    /**
     * The verified user.
     *
     * @var \WpStarter\Contracts\Auth\MustVerifyEmail
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Contracts\Auth\MustVerifyEmail  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
