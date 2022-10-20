<?php

namespace WpStarter\Wordpress;

use WpStarter\Database\Connection;
use WpStarter\Routing\Redirector;
use WpStarter\Support\ServiceProvider;
use WpStarter\Wordpress\Auth\User;
use WpStarter\Wordpress\Console\Commands\Database\MigrationWipeCommand;
use WpStarter\Wordpress\Database\WpConnection;
use WpStarter\Wordpress\Database\WpConnector;
use WpStarter\Wordpress\Dependency\ResourceManager;
use WpStarter\Wordpress\Http\Response\Handler;
use WpStarter\Wordpress\Http\Response\PassThrough;
use WpStarter\Wordpress\Mail\Transport\WpTransport;
use WpStarter\Wordpress\Routing\RoutingServiceProvider;

class WordpressServiceProvider extends ServiceProvider
{
    function register(){
        $this->configureDatabase();
        $this->registerResourceManager();
        $this->extendMigrationCommands();
        $this->registerMailerTransport();
        $this->registerResponse();
        $this->registerChildServices();
    }
    protected function registerChildServices(){
        $this->app->register(RoutingServiceProvider::class);
    }
    function boot(){
        if(!defined('ABSPATH')){
            return ;
        }
        User::setConnectionResolver($this->app['db']);
        User::setEventDispatcher($this->app['events']);
        $this->bootResourceManager();
    }
    protected function registerResponse(){
        $this->app->singleton(Handler::class);
        Redirector::macro('pass',function(){
            return new PassThrough();
        });
    }
    protected function registerMailerTransport(){
        $this->app->resolving('mail.manager',function($mailManager){
            $mailManager->extend('wp',function($config){
                return new WpTransport($config);
            });
        });
    }
    protected function configureDatabase(){
        $this->app->alias(WpConnector::class,'db.connector.wp');
        Connection::resolverFor('wp',function($connection, $database, $prefix, $config){
            return new WpConnection($connection, $database, $prefix, $config);
        });
    }
    /**
     * Register the command.
     *
     * @return void
     */
    protected function extendMigrationCommands()
    {
        //Custom wipe command then it wipe thing which create by WpStarter only
        $this->app->extend('command.db.wipe', function ($migrator, $app) {
            return new MigrationWipeCommand($app['migrator']);
        });
    }
    protected function registerResourceManager(){
        $this->app->singleton('resources',function(){
            return new ResourceManager($this->app);
        });
        $this->app->alias('resources',ResourceManager::class);
    }
    protected function bootResourceManager(){
        $this->app->make(ResourceManager::class)->boot();
    }
}