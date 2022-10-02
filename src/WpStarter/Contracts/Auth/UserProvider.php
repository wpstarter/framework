<?php

namespace WpStarter\Contracts\Auth;

interface UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \WpStarter\Wordpress\User|null
     */
    public function retrieveById($identifier);
}
