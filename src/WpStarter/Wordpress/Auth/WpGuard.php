<?php

namespace WpStarter\Wordpress\Auth;

use WpStarter\Auth\GuardHelpers;
use WpStarter\Contracts\Auth\Authenticatable;
use WpStarter\Contracts\Auth\StatefulGuard;
use WpStarter\Support\Traits\Macroable;

class WpGuard implements StatefulGuard
{
    use GuardHelpers, Macroable;
    public $lastAttempted;
    public function __construct(WpUserProvider $provider)
    {
        $this->provider=$provider;
    }

    /**
     * @param \WpStarter\Contracts\Auth\Authenticatable $user
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        wp_set_current_user($user->getAuthIdentifier());
        return $this;
    }

    public function user()
    {
        if(!is_null($this->user)){
            return $this->user;
        }
        $wpUser=wp_get_current_user();
        if($wpUser && $wpUser->ID) {//WordPress may have ID=0
            $this->user = $this->provider->retrieveById($wpUser);
        }
        return $this->user;
    }

    public function validate(array $credentials = [])
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        return $this->provider->validateCredentials($user, $credentials);
    }
    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        do_action_ref_array( 'wp_authenticate', array( &$credentials['user_login'], &$credentials['user_password'] ) );
        if($this->lastAttempted=$user=$this->provider->retrieveByCredentials($credentials)){
            //We use wp_authenticate() to check to keep the hooks triggered
            if($this->provider->validateCredentialsByWpAuthenticate($user, $credentials)){
                $this->login($user, $remember);
                return true;
            }
        }
        return false;
    }
    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            return true;
        }
        return false;
    }
    /**
     * Log a user into the application.
     *
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user
     * @param  bool  $remember
     * @return void
     */
    public function login(Authenticatable $user, $remember = false)
    {
        wp_set_auth_cookie($user->getAuthIdentifier(), $remember);
        do_action('wp_login', $user->user_login, $user);
        $this->setUser($user);
    }
    /**
     * Log the given user ID into the application.
     *
     * @param  mixed  $id
     * @param  bool  $remember
     * @return \WpStarter\Contracts\Auth\Authenticatable|false
     */
    public function loginUsingId($id, $remember = false)
    {
        if($user=$this->provider->retrieveById($id)){
            $this->login($user, $remember);
            return $user;
        }
        return false;
    }
    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  mixed  $id
     * @return \WpStarter\Contracts\Auth\Authenticatable|false
     */
    public function onceUsingId($id)
    {
        if($user=$this->provider->retrieveById($id)){
            $this->setUser($user);
            return $user;
        }
        return false;
    }
    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        //Always return false because WordPress does not support "remember me" cookie
        return false;
    }
    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        wp_logout();
        $this->user=null;
    }
}
