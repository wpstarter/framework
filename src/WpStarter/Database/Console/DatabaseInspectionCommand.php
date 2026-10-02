<?php

namespace WpStarter\Database\Console;

use WpStarter\Console\Command;
use WpStarter\Database\ConnectionInterface;
use WpStarter\Support\Arr;

abstract class DatabaseInspectionCommand extends Command
{
    /**
     * Get a human-readable name for the given connection.
     *
     * @param  \WpStarter\Database\ConnectionInterface  $connection
     * @param  string  $database
     * @return string
     *
     * @deprecated
     */
    protected function getConnectionName(ConnectionInterface $connection, $database)
    {
        return $connection->getDriverTitle();
    }

    /**
     * Get the number of open connections for a database.
     *
     * @param  \WpStarter\Database\ConnectionInterface  $connection
     * @return int|null
     *
     * @deprecated
     */
    protected function getConnectionCount(ConnectionInterface $connection)
    {
        return $connection->threadCount();
    }

    /**
     * Get the connection configuration details for the given connection.
     *
     * @param  string|null  $database
     * @return array
     */
    protected function getConfigFromDatabase($database)
    {
        $database ??= ws_config('database.default');

        return Arr::except(ws_config('database.connections.'.$database), ['password']);
    }
}
