<?php

namespace WpStarter\Queue\Connectors;

interface ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \WpStarter\Contracts\Queue\Queue
     */
    public function connect(array $config);
}
