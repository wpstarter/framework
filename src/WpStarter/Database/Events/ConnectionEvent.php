<?php

namespace WpStarter\Database\Events;

abstract class ConnectionEvent
{
    /**
     * The name of the connection.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The database connection instance.
     *
     * @var \WpStarter\Database\Connection
     */
    public $connection;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Database\Connection  $connection
     * @return void
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }
}
