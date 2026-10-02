<?php

namespace WpStarter\Tests\Integration\Database\MySql;

use WpStarter\Tests\Integration\Database\DatabaseTestCase;
use Orchestra\Testbench\Attributes\RequiresDatabase;

#[RequiresDatabase('mysql')]
abstract class MySqlTestCase extends DatabaseTestCase
{
    //
}
