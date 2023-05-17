<?php

namespace WpStarter\Wordpress\Middleware;

class StartSession extends \WpStarter\Session\Middleware\StartSession
{
    protected function saveSession($request)
    {
        if (function_exists('add_action')){
            add_action('shutdown', function () use ($request) {
                parent::saveSession($request);
            });
        }else{
            register_shutdown_function(function () use ($request) {
                parent::saveSession($request);
            });
        }

    }
}
