<?php

namespace WpStarter\Wordpress\Database;

use WpStarter\Database\Connectors\MySqlConnector;

class WpConnector extends MySqlConnector
{
    /**
     * Create a new PDO connection instance.
     *
     * @param  string  $dsn
     * @param  string  $username
     * @param  string  $password
     * @param  array  $options
     * @return \PDO
     */
    protected function createPdoConnection($dsn, $username, $password, $options)
    {
        global $wpdb;
        return new WpPdo($wpdb);
    }
}