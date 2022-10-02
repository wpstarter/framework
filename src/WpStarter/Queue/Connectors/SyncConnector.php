<?php

namespace WpStarter\Queue\Connectors;

use WpStarter\Queue\SyncQueue;

class SyncConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \WpStarter\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new SyncQueue;
    }
}
