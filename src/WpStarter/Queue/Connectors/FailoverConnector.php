<?php

namespace WpStarter\Queue\Connectors;

use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Queue\FailoverQueue;
use WpStarter\Queue\QueueManager;

class FailoverConnector implements ConnectorInterface
{
    /**
     * Create a new connector instance.
     */
    public function __construct(
        protected QueueManager $manager,
        protected Dispatcher $events
    ) {
    }

    /**
     * Establish a queue connection.
     *
     * @return \WpStarter\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new FailoverQueue(
            $this->manager,
            $this->events,
            $config['connections'],
        );
    }
}
