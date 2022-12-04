<?php

namespace WpStarter\Wordpress\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static \WpStarter\Wordpress\Shortcode\ShortcodeManager add($shortcode, $callable = null)
 * @method static \WpStarter\Wordpress\Shortcode\ShortcodeManager setBootHook($hook, $priority = 10)
 * @method static \WpStarter\Wordpress\View\Shortcode get($tag)
 * @method static boolean has($tag)
 */
class Shortcode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.shortcode';
    }
}