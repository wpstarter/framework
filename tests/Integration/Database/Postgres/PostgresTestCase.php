<?php

namespace WpStarter\Tests\Integration\Database\Postgres;

use WpStarter\Tests\Integration\Database\DatabaseTestCase;

abstract class PostgresTestCase extends DatabaseTestCase
{
    protected function defineDatabaseMigrations()
    {
        if ($this->driver !== 'pgsql') {
            $this->markTestSkipped('Test requires a PostgreSQL connection.');
        }
    }
}
