<?php

namespace WpStarter\Wordpress\Middleware;

class StartSession extends \WpStarter\Session\Middleware\StartSession
{
    protected function saveSession($request)
    {
        add_action('shutdown', function () use ($request) {
            parent::saveSession($request);
        });

    }
}