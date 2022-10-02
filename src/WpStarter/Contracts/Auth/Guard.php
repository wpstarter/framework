<?php

namespace WpStarter\Contracts\Auth;

interface Guard
{
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
