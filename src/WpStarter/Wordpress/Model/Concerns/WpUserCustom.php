<?php

namespace WpStarter\Wordpress\Model\Concerns;

trait WpUserCustom
{
    /**
     * @param $field
     * @param $value
     * @return static|null
     */
    public static function findBy($field, $value)
    {
        $data = static::get_data_by($field, $value);
        if ($data) {
            $model = new static();
            $model->init($data);
            return $model;
        }
        return null;
    }

    /**
     * This find function will use wp core function to do and have object caching by default
     * @param $id
     * @param $site_id
     * @return static|null
     */
    public static function find($id, $site_id = '')
    {
        if ($id instanceof static) {
            $id = $id->ID;
        }
        $data = static::get_data_by('id', $id);

        if ($data) {
            $model = new static();
            $model->init($data, $site_id);
            return $model;
        }
        return null;
    }

    /**
     * @param \WP_User|null $wp_user
     * @return static|null
     */
    public static function fromWpUser(?\WP_User $wp_user)
    {
        if (!$wp_user) {
            return null;
        }
        $user = new static();
        $user->init($wp_user->data, $wp_user->get_site_id());
        return $user;
    }
}
