<?php

namespace WpStarter\Auth;

use WpStarter\Contracts\Auth\UserProvider;

class WpGuard
{

    /**
     * Create a new authentication guard.
     *
     * @param  string  $name
     * @param  \WpStarter\Contracts\Auth\UserProvider  $provider
     * @return void
     */
    public function __construct($name,
                                UserProvider $provider)
    {
        $this->name = $name;
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \WpStarter\Wordpress\User|null
     */
    public function user()
    {
        return $this->provider->retrieveById(wp_get_current_user());
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        return $this->user() ? $this->user()->ID:null;
    }

}
