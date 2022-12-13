<?php

namespace WpStarter\Wordpress\Admin;

use WpStarter\Routing\Redirector;
use WpStarter\Routing\UrlGenerator;
use WpStarter\Support\ServiceProvider;
use WpStarter\Wordpress\Admin\Contracts\Kernel;
use WpStarter\Wordpress\Admin\Notice\NoticeManager;
use WpStarter\Wordpress\Admin\Notice\SessionStore;
use WpStarter\Wordpress\Admin\Routing\Router;

class AdminServiceProvider extends ServiceProvider
{
    function register()
    {
        $this->app->singleton('wp.admin.router', function ($app) {
            return new Router($app['events'], $app);
        });
        $this->app->alias('wp.admin.router', Router::class);

        $this->app->singleton('wp.admin.notice',function($app){
            return new NoticeManager(new SessionStore($app['session']));
        });
        $this->app->alias('wp.admin.notice',NoticeManager::class);
        UrlGenerator::macro('admin',function($slug=null,$params=[]){
            if(!$slug){
                if($menu=ws_app('wp.admin.router')->current()){
                    $slug=$menu->slug;
                }
            }
            $url=menu_page_url($slug,false);
            $params=array_filter($params, function ($p) {
                return ! is_null($p);
            });
            return add_query_arg($params,$url);
        });
        Redirector::macro('admin',function($slug=null,$params=[],$status=302,$headers=[]){
            return $this->createRedirect($this->generator->admin($slug,$params), $status, $headers);
        });
    }

    function boot()
    {
        if(!is_wp()){
            return ;
        }
        if($this->app->bound(Kernel::class)) {
            $this->app->make(Kernel::class)->handle($this->app['request']);
            $this->loadViewsFrom(__DIR__ . '/resources/views', 'wp.admin');
        }
    }
}