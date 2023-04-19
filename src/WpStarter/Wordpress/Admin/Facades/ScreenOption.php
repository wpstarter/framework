<?php

namespace WpStarter\Wordpress\Admin\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static \WpStarter\Wordpress\Admin\Services\ScreenOption add($option,$callback=false)
 */
class ScreenOption extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.admin.screen_option';
    }
}
