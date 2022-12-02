<?php

namespace WpStarter\Wordpress\Facades;

use WpStarter\Support\Facades\Facade;
use WpStarter\Wordpress\Shortcode\ShortcodeManager;

/**
 * @method static \WpStarter\Wordpress\Shortcode\ShortcodeManager add($shortcode,$callable)
 */
class Shortcode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.shortcode';
    }
}