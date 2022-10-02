<?php

namespace WpStarter\Auth;

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
            }
        }
        else{
            $model=$this->createModel();
            return $model->find($identifier);
        }
        return null;
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
}
