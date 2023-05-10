<?php

namespace WpStarter\Wordpress\Admin\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static \WpStarter\Wordpress\Admin\Services\ScreenOption add(array|string $options,\Closure $callback=null)
 */
class ScreenOption extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.admin.screen_option';
    }

}
