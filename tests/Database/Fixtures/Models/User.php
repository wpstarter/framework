<?php

namespace WpStarter\Tests\Database\Fixtures\Models;

use WpStarter\Foundation\Auth\User as FoundationUser;

class User extends FoundationUser
{
    protected $primaryKey = 'internal_id';
}
