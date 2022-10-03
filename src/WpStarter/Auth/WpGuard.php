<?php

namespace WpStarter\Auth;

use WpStarter\Contracts\Auth\UserProvider;

class WpGuard
{
    protected $name;
    protected $provider;
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
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check(){
        if($this->user()){
            return true;
        }
        return false;
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest(){
        return !$this->check();
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
