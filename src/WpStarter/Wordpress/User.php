<?php

namespace WpStarter\Wordpress;

use WpStarter\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use WpStarter\Contracts\Auth\Authenticatable;
use WpStarter\Database\Eloquent\Collection;
use WpStarter\Wordpress\Auth\Access\Authorizable;
use WpStarter\Wordpress\Auth\User as Model;

class User extends Model implements AuthorizableContract, Authenticatable
{
    use Authorizable;
    /**
     * @param \WP_User|null $wp_user
     * @return static|null
     */
    public static function fromWpUser(\WP_User $wp_user=null){
        if(!$wp_user){
            return null;
        }
        $user=new static();
        $user->init($wp_user->data,$wp_user->get_site_id());
        return $user;
    }
    /**
     * @param $email
     * @return Collection|User[]
     */
    public static function findByEmail($email){
        $args = array(
            'search_columns'=>['email'],
            'search'=>$email,
        );
        $userArray = get_users($args);
        $users = array();
        foreach($userArray as $user){
            $users[] = static::fromWpUser($user);
        }
        return Collection::make($users);
    }

    /**
     * @param $login
     * @return static|null
     */
    public static function findByLogin($login){
        return static::findBy('user_login',$login);
    }

    /**
     * @param $id
     * @return static|null
     */
    public static function findById($id){
        return static::findBy('id',$id);
    }

    /**
     * @param $field
     * @param $value
     * @return static|null
     */
    public static function findBy($field,$value){
        $data=static::get_data_by($field,$value);
        if($data){
            $model = new static();
            $model->init($data);
            return $model;
        }
        return null;
    }

}