<?php

namespace WpStarter\Wordpress\Auth;

use WpStarter\Wordpress\Model\User as Model;
use WpStarter\Auth\MustVerifyEmail;
use WpStarter\Auth\Passwords\CanResetPassword;
use WpStarter\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use WpStarter\Contracts\Auth\Authenticatable as AuthenticatableContract;
use WpStarter\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use WpStarter\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable, Authorizable;
    /**
     * @param \WP_User|null $wp_user
     * @return static|null
     */
    public static function fromWpUser(\WP_User $wp_user = null)
    {
        if (!$wp_user) {
            return null;
        }
        $user = new static();
        $user->init($wp_user->data, $wp_user->get_site_id());
        return $user;
    }
}
