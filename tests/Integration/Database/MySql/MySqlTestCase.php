<?php

namespace WpStarter\Tests\Integration\Database\MySql;

use WpStarter\Tests\Integration\Database\DatabaseTestCase;

abstract class MySqlTestCase extends DatabaseTestCase
{
    protected function defineDatabaseMigrations()
    {
        if ($this->driver !== 'mysql') {
            $this->markTestSkipped('Test requires a MySQL connection.');
        }
    }
}
