<?php

namespace WpStarter\Contracts\Redis;

interface Factory
{
    /**
     * Get a Redis connection by name.
     *
     * @param  string|null  $name
     * @return \WpStarter\Redis\Connections\Connection
     */
    public function connection($name = null);
}
