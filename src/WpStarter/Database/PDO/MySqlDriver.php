<?php

namespace WpStarter\Database\PDO;

use Doctrine\DBAL\Driver\AbstractMySQLDriver;
use WpStarter\Database\PDO\Concerns\ConnectsToDatabase;

class MySqlDriver extends AbstractMySQLDriver
{
    use ConnectsToDatabase;
}
