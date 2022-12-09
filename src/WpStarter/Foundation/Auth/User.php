<?php

namespace WpStarter\Foundation\Auth;

use WpStarter\Auth\Authenticatable;
use WpStarter\Auth\MustVerifyEmail;
use WpStarter\Auth\Passwords\CanResetPassword;
use WpStarter\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use WpStarter\Contracts\Auth\Authenticatable as AuthenticatableContract;
use WpStarter\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
}
