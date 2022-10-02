<?php

namespace WpStarter\Wordpress\Auth\Concerns;

class Support
{
    public static function dataMerge($data,$extra){
        foreach ($extra as $key=>$value){
            $data->{$key}=$value;
        }
        return $data;
    }
    public static function dataMap($callback,$data){
        foreach ($data as $key=>$value){
            $data->{$key}=$callback($value);
        }
        return $data;
    }
    public static function dataKeyExists($key,$data){
        return array_key_exists($key,(array)$data);
    }
}