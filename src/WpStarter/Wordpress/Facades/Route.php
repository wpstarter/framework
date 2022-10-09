<?php

namespace WpStarter\Wordpress\Facades;

use WpStarter\Support\Facades\Facade;

class Route extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.router';
    }
}