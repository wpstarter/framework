<?php

namespace WpStarter\Wordpress\Auth;

use WpStarter\Auth\AuthManager;
use WpStarter\Support\Facades\Gate;
use WpStarter\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    function register(){

    }
    function boot(){
        if($this->app->bound('auth')){

            $authManager=$this->app->make('auth');
            /**
             * @var AuthManager $authManager
             */
            $authManager->extend('wp',function($app,$name,$config)use($authManager){
                $provider = $authManager->createUserProvider($config['provider'] ?? null);
                return new WpGuard($provider);
            });
            $authManager->provider('wp',function($app,$config){
                return new WpUserProvider($config['model']);
            });
            if(function_exists('user_can')) {
                Gate::after(function ($user, $ability, $result, $arguments) {
                    return user_can($user, $ability, ...$arguments);
                });
            }
        }
    }
}