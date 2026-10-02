<?php

namespace WpStarter\Tests\Integration\Database;

use WpStarter\Foundation\Testing\DatabaseMigrations;

class EloquentTransactionWithAfterCommitUsingDatabaseMigrationsTest extends DatabaseTestCase
{
    use EloquentTransactionWithAfterCommitTests;
    use DatabaseMigrations;
}
