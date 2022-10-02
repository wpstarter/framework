<?php

namespace WpStarter\Wordpress;

use WpStarter\Database\Connection;
use WpStarter\Support\ServiceProvider;
use WpStarter\Wordpress\Auth\User;
use WpStarter\Wordpress\Database\WpConnection;
use WpStarter\Wordpress\Database\WpConnector;

class WordpressServiceProvider extends ServiceProvider
{
    function register(){
        $this->app->alias(WpConnector::class,'db.connector.wp');
        Connection::resolverFor('wp',function($connection, $database, $prefix, $config){
            return new WpConnection($connection, $database, $prefix, $config);
        });
    }
    function boot(){
        User::setConnectionResolver($this->app['db']);
        User::setEventDispatcher($this->app['events']);
    }
}