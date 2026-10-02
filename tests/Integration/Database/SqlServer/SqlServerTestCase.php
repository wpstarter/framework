<?php

namespace WpStarter\Tests\Integration\Database\SqlServer;

use WpStarter\Tests\Integration\Database\DatabaseTestCase;
use Orchestra\Testbench\Attributes\RequiresDatabase;

#[RequiresDatabase('sqlsrv')]
abstract class SqlServerTestCase extends DatabaseTestCase
{
    //
}
