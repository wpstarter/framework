<?php

namespace WpStarter\Wordpress;

use WpStarter\Database\Connection;
use WpStarter\Routing\Redirector;
use WpStarter\Support\Facades\Artisan;
use WpStarter\Support\ServiceProvider;
use WpStarter\Wordpress\Admin\AdminServiceProvider;
use WpStarter\Wordpress\Auth\AuthServiceProvider;
use WpStarter\Wordpress\Console\Commands\Database\MigrationWipeCommand;
use WpStarter\Wordpress\Database\WpConnection;
use WpStarter\Wordpress\Database\WpConnector;
use WpStarter\Wordpress\Dependency\ResourceManager;
use WpStarter\Wordpress\Http\Response\Handler;
use WpStarter\Wordpress\Http\Response\PassThrough;
use WpStarter\Wordpress\Mail\Transport\WpTransport;
use WpStarter\Wordpress\Model\User;
use WpStarter\Wordpress\Providers\CarbonServiceProvider;
use WpStarter\Wordpress\Routing\RoutingServiceProvider;
use WpStarter\Wordpress\Services\Livewire;
use WpStarter\Wordpress\Shortcode\ShortcodeManager;
use WpStarter\Wordpress\Translation\TranslationServiceProvider;

class WordpressServiceProvider extends ServiceProvider
{
    function register()
    {
        $this->configureDatabase();
        $this->registerResourceManager();
        $this->extendMigrationCommands();
        $this->registerMailerTransport();
        $this->registerResponse();
        $this->registerShortcodeManager();
        $this->registerChildServices();

    }

    protected function registerChildServices()
    {
        if(class_exists(\Carbon\Carbon::class)){
            $this->app->register(CarbonServiceProvider::class);
        }
        $this->app->register(RoutingServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(AdminServiceProvider::class);
        $this->app->register(TranslationServiceProvider::class);
    }

    function boot()
    {
        User::setConnectionResolver($this->app['db']);
        User::setEventDispatcher($this->app['events']);
        if (!is_wp()) {
            return;
        }
        $this->bootServices();
        $this->autoRestartQueue();
    }

    protected function bootServices(){
        $this->bootResourceManager();
        $this->bootShortcodeManager();
    }
    protected function autoRestartQueue(){
        add_action('activated_plugin',function(){
            Artisan::call('queue:restart');
        });
        add_action('deactivated_plugin',function(){
            Artisan::call('queue:restart');
        });
    }

    protected function registerResponse()
    {
        $this->app->singleton(Handler::class);
        Redirector::macro('pass', function () {
            return new PassThrough();
        });
    }

    protected function registerMailerTransport()
    {
        $this->app->resolving('mail.manager', function ($mailManager) {
            $mailManager->extend('wp', function ($config) {
                return new WpTransport($config);
            });
        });
    }

    protected function registerShortcodeManager()
    {
        $this->app->singleton(ShortcodeManager::class);
        $this->app->alias(ShortcodeManager::class, 'wp.shortcode');
    }

    protected function bootShortcodeManager()
    {
        $this->app->make(ShortcodeManager::class)->boot();
    }

    protected function configureDatabase()
    {
        $this->app->alias(WpConnector::class, 'db.connector.wp');
        Connection::resolverFor('wp', function ($connection, $database, $prefix, $config) {
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

    protected function registerResourceManager()
    {
        $this->app->singleton('resources', function () {
            return new ResourceManager($this->app);
        });
        $this->app->alias('resources', ResourceManager::class);

        $this->app->singleton(Livewire::class);
        $this->app->alias(Livewire::class,'wp.livewire');
    }

    protected function bootResourceManager()
    {
        $this->app->make(ResourceManager::class)->boot();
    }
}
