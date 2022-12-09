<?php

namespace WpStarter\Wordpress;

use WpStarter\Wordpress\Auth\User as UserModel;
use WpStarter\Wordpress\Model\WpUserQuery;

class User extends UserModel
{
    use WpUserQuery;
}