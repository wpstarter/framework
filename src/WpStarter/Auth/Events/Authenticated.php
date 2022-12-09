<?php

namespace WpStarter\Auth\Events;

use WpStarter\Queue\SerializesModels;

class Authenticated
{
    use SerializesModels;

    /**
     * The authentication guard name.
     *
     * @var string
     */
    public $guard;

    /**
     * The authenticated user.
     *
     * @var \WpStarter\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  string  $guard
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($guard, $user)
    {
        $this->user = $user;
        $this->guard = $guard;
    }
}
