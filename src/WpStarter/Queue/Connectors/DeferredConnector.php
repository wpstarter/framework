<?php

namespace WpStarter\Queue\Connectors;

use WpStarter\Queue\DeferredQueue;

class DeferredConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @return \WpStarter\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new DeferredQueue($config['after_commit'] ?? null);
    }
}
