<?php

namespace WpStarter\Contracts\Broadcasting;

interface Factory
{
    /**
     * Get a broadcaster implementation by name.
     *
     * @param  string|null  $name
     * @return \WpStarter\Contracts\Broadcasting\Broadcaster
     */
    public function connection($name = null);
}
