<?php

namespace WpStarter\Wordpress\Auth;

use WpStarter\Contracts\Auth\Authenticatable;
use WpStarter\Contracts\Auth\UserProvider;

class WpUserProvider implements UserProvider
{

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * Create a new database user provider.
     *
     * @param  string  $model
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \WpStarter\Wordpress\User|null
     */
    public function retrieveById($identifier)
    {
        $class = '\\'.ltrim($this->model, '\\');
        if($identifier instanceof \WP_User){
            if(is_callable([$class,'fromWpUser'])) {
                return $class::fromWpUser($identifier);
            }else{
                $identifier=$identifier->ID;
            }
        }
        $model=$this->createModel();
        return $model->find($identifier);
    }


    /**
     * Create a new instance of the model.
     *
     * @return \WpStarter\Wordpress\User
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  string  $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }


    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \WpStarter\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if(isset($credentials['user_id'])){
            $user=get_user_by('id', $credentials['user_login']);
            if($user){
                return $this->retrieveById($user);
            }
        }elseif(isset($credentials['user_slug'])){
            $user=get_user_by('slug', $credentials['user_login']);
            if($user){
                return $this->retrieveById($user);
            }
        }elseif(isset($credentials['user_login'])){
            $user=get_user_by('login', $credentials['user_login']);
            if($user){
                return $this->retrieveById($user);
            }
        }elseif(isset($credentials['username'])){
            $user=get_user_by('login', $credentials['username']);
            if($user){
                return $this->retrieveById($user);
            }
        }
        elseif(isset($credentials['user_email'])){
            $user=get_user_by('email', $credentials['user_email']);
            if($user){
                return $this->retrieveById($user);
            }
        }
        return null;
    }
    /**
     * Validate a user against the given credentials.
     *
     * @param  \WpStarter\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $password=$credentials['user_password']??$credentials['user_pass']??$credentials['password']??null;
        if(!$password){
            return false;
        }
        return wp_check_password($password,$user->getAuthPassword(),$user->getAuthIdentifier());
    }
}