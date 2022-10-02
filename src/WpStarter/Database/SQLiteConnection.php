<?php

namespace WpStarter\Database;

use Doctrine\DBAL\Driver\PDOSqlite\Driver as DoctrineDriver;
use Doctrine\DBAL\Version;
use WpStarter\Database\PDO\SQLiteDriver;
use WpStarter\Database\Query\Grammars\SQLiteGrammar as QueryGrammar;
use WpStarter\Database\Query\Processors\SQLiteProcessor;
use WpStarter\Database\Schema\Grammars\SQLiteGrammar as SchemaGrammar;
use WpStarter\Database\Schema\SQLiteBuilder;
use WpStarter\Database\Schema\SqliteSchemaState;
use WpStarter\Filesystem\Filesystem;

class SQLiteConnection extends Connection
{
    /**
     * Create a new database connection instance.
     *
     * @param  \PDO|\Closure  $pdo
     * @param  string  $database
     * @param  string  $tablePrefix
     * @param  array  $config
     * @return void
     */
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);

        $enableForeignKeyConstraints = $this->getForeignKeyConstraintsConfigurationValue();

        if ($enableForeignKeyConstraints === null) {
            return;
        }

        $enableForeignKeyConstraints
            ? $this->getSchemaBuilder()->enableForeignKeyConstraints()
            : $this->getSchemaBuilder()->disableForeignKeyConstraints();
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \WpStarter\Database\Query\Grammars\SQLiteGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \WpStarter\Database\Schema\SQLiteBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new SQLiteBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \WpStarter\Database\Schema\Grammars\SQLiteGrammar
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
     *
     * @throws \RuntimeException
     */
    public function getSchemaState(Filesystem $files = null, callable $processFactory = null)
    {
        return new SqliteSchemaState($this, $files, $processFactory);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \WpStarter\Database\Query\Processors\SQLiteProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new SQLiteProcessor;
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Doctrine\DBAL\Driver\PDOSqlite\Driver|\WpStarter\Database\PDO\SQLiteDriver
     */
    protected function getDoctrineDriver()
    {
        return class_exists(Version::class) ? new DoctrineDriver : new SQLiteDriver;
    }

    /**
     * Get the database connection foreign key constraints configuration option.
     *
     * @return bool|null
     */
    protected function getForeignKeyConstraintsConfigurationValue()
    {
        return $this->getConfig('foreign_key_constraints');
    }
}
