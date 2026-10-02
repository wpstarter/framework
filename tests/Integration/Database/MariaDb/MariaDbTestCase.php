<?php

namespace WpStarter\Tests\Integration\Database\MariaDb;

use WpStarter\Tests\Integration\Database\DatabaseTestCase;
use Orchestra\Testbench\Attributes\RequiresDatabase;

#[RequiresDatabase('mariadb')]
abstract class MariaDbTestCase extends DatabaseTestCase
{
    //
}
