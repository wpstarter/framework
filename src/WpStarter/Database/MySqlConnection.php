<?php

namespace WpStarter\Database;

use Doctrine\DBAL\Driver\PDOMySql\Driver as DoctrineDriver;
use Doctrine\DBAL\Version;
use WpStarter\Database\PDO\MySqlDriver;
use WpStarter\Database\Query\Grammars\MySqlGrammar as QueryGrammar;
use WpStarter\Database\Query\Processors\MySqlProcessor;
use WpStarter\Database\Schema\Grammars\MySqlGrammar as SchemaGrammar;
use WpStarter\Database\Schema\MySqlBuilder;
use WpStarter\Database\Schema\MySqlSchemaState;
use WpStarter\Filesystem\Filesystem;
use PDO;

class MySqlConnection extends Connection
{
    /**
     * Determine if the connected database is a MariaDB database.
     *
     * @return bool
     */
    public function isMaria()
    {
        return strpos($this->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), 'MariaDB') !== false;
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \WpStarter\Database\Query\Grammars\MySqlGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \WpStarter\Database\Schema\MySqlBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new MySqlBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \WpStarter\Database\Schema\Grammars\MySqlGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new SchemaGrammar);
    }

    /**
     * Get the schema state for the connection.
     *
     * @param  \WpStarter\Filesystem\Filesystem|null  $files
     * @param  callable|null  $processFactory
     * @return \WpStarter\Database\Schema\MySqlSchemaState
     */
    public function getSchemaState(Filesystem $files = null, callable $processFactory = null)
    {
        return new MySqlSchemaState($this, $files, $processFactory);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \WpStarter\Database\Query\Processors\MySqlProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new MySqlProcessor;
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Doctrine\DBAL\Driver\PDOMySql\Driver|\WpStarter\Database\PDO\MySqlDriver
     */
    protected function getDoctrineDriver()
    {
        return class_exists(Version::class) ? new DoctrineDriver : new MySqlDriver;
    }
}
