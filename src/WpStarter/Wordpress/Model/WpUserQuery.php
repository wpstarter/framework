<?php

namespace WpStarter\Wordpress\Model;

trait WpUserQuery
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
}