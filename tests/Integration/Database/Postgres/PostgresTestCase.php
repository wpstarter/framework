<?php

namespace WpStarter\Tests\Integration\Database\Postgres;

use WpStarter\Tests\Integration\Database\DatabaseTestCase;
use Orchestra\Testbench\Attributes\RequiresDatabase;

#[RequiresDatabase('pgsql')]
abstract class PostgresTestCase extends DatabaseTestCase
{
    //
}
