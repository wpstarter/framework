<?php

namespace WpStarter\Contracts\Auth;

interface Guard
{
    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check();

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest();
    /**
     * Get the currently authenticated user.
     *
     * @return \WpStarter\Wordpress\User|null
     */
    public function user();

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id();

}
