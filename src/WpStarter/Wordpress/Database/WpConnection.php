<?php


namespace WpStarter\Wordpress\Database;

use DateTime;
use Exception;
use JsonSerializable;
use Serializable;
use WpStarter\Database\MySqlConnection;
use WpStarter\Database\QueryException;

class WpConnection extends MySqlConnection
{
    /**
     * The active PDO connection.
     *
     * @var WpPdo
     */
    protected $pdo;

    /**
     * @return WpPdo|\Closure|\PDO
     */
    function getPdo()
    {
        return parent::getPdo();
    }
}
