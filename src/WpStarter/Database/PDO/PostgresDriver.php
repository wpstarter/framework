<?php

namespace WpStarter\Database\PDO;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use WpStarter\Database\PDO\Concerns\ConnectsToDatabase;

class PostgresDriver extends AbstractPostgreSQLDriver
{
    use ConnectsToDatabase;
}
