<?php

namespace WpStarter\Database\PDO;

use Doctrine\DBAL\Driver\AbstractSQLiteDriver;
use WpStarter\Database\PDO\Concerns\ConnectsToDatabase;

class SQLiteDriver extends AbstractSQLiteDriver
{
    use ConnectsToDatabase;
}
