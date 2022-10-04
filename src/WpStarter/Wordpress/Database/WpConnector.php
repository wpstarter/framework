<?php

namespace WpStarter\Wordpress\Database;

use PDO;
use WpStarter\Database\Connectors\Connector;
use WpStarter\Database\Connectors\ConnectorInterface;

class WpConnector extends Connector implements ConnectorInterface
{

    public function connect(array $config)
    {
        $dsn = '';

        $options = $this->getOptions($config);

        // We need to grab the PDO options that should be used while making the brand
        // new connection instance. The PDO options control various aspects of the
        // connection's behavior, and some might be specified by the developers.
        $connection = $this->createConnection($dsn, $config, $options);

        $this->configureDatabase($connection, $config);

        $this->configureIsolationLevel($connection, $config);

        $this->configureEncoding($connection, $config);

        // Next, we will check to see if a timezone has been specified in this config
        // and if it has we will issue a statement to modify the timezone with the
        // database. Setting this DB timezone is an optional configuration item.
        $this->configureTimezone($connection, $config);

        $this->setModes($connection, $config);

        return $connection;
    }
    /**
     * Set the database name.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function configureDatabase($connection, array $config){
        if (! empty($config['database'])) {
            if(defined('DB_NAME') && $config['database'] !== DB_NAME){
                $connection->exec("use `{$config['database']}`;");
            }
        }
    }

    /**
     * Set the connection transaction isolation level.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function configureIsolationLevel($connection, array $config)
    {
        if (! isset($config['isolation_level'])) {
            return;
        }

        $connection->prepare(
            "SET SESSION TRANSACTION ISOLATION LEVEL {$config['isolation_level']}"
        )->execute();
    }

    /**
     * Set the connection character set and collation.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void|\PDO
     */
    protected function configureEncoding($connection, array $config)
    {
        if (! isset($config['charset'])) {
            return $connection;
        }

        $connection->prepare(
            "set names '{$config['charset']}'".$this->getCollation($config)
        )->execute();
    }
    /**
     * Get the collation for the configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getCollation(array $config)
    {
        return isset($config['collation']) ? " collate '{$config['collation']}'" : '';
    }
    /**
     * Set the timezone on the connection.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function configureTimezone($connection, array $config)
    {
        if (isset($config['timezone'])) {
            $connection->prepare('set time_zone="'.$config['timezone'].'"')->execute();
        }
    }

    /**
     * Set the modes for the connection.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function setModes(PDO $connection, array $config)
    {
        if (isset($config['modes'])) {
            $this->setCustomModes($connection, $config);
        } elseif (isset($config['strict'])) {
            if ($config['strict']) {
                $connection->prepare($this->strictMode($connection, $config))->execute();
            } else {
                $connection->prepare("set session sql_mode='NO_ENGINE_SUBSTITUTION'")->execute();
            }
        }
    }
    /**
     * Set the custom modes on the connection.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function setCustomModes(PDO $connection, array $config)
    {
        $modes = implode(',', $config['modes']);

        $connection->prepare("set session sql_mode='{$modes}'")->execute();
    }

    /**
     * Get the query to enable strict mode.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return string
     */
    protected function strictMode(PDO $connection, $config)
    {
        $version = $config['version'] ?? $connection->getAttribute(PDO::ATTR_SERVER_VERSION);

        if (version_compare($version, '8.0.11') >= 0) {
            return "set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'";
        }

        return "set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'";
    }
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