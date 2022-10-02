<?php

namespace WpStarter\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \WpStarter\Contracts\Queue\Queue
     */
    public function connection($name = null);
}
