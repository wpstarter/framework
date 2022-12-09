<?php

namespace WpStarter\Wordpress\Auth;

use WpStarter\Auth\GuardHelpers;
use WpStarter\Contracts\Auth\Guard;
use WpStarter\Contracts\Auth\UserProvider;

class WpGuard implements Guard
{
    use GuardHelpers;
    public function __construct(UserProvider $provider)
    {
        $this->provider=$provider;
    }

    public function user()
    {
        if(!is_null($this->user)){
            return $this->user;
        }
        return $this->provider->retrieveById(wp_get_current_user());
    }

    public function validate(array $credentials = [])
    {
        return wp_authenticate($credentials['username'],$credentials['password']);
    }
}