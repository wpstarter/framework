<?php

namespace WpStarter\Wordpress\Admin\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * Class Layout
 * @package WpStarter\WordPress\Admin\Facades
 * @method static \WpStarter\Wordpress\Admin\View\Layout title($title)
 * @method static \WpStarter\Wordpress\Admin\View\Layout subTitle($subtitle)
 * @method static \WpStarter\Wordpress\Admin\View\Layout action($text,$link,$desc='')
 * @method static \WpStarter\Wordpress\Admin\View\Layout setNoticeManager($manager)
 * @method static array getNotices()
 * @method static mixed clearNotices()
 * @method static string getTitle()
 * @method static string getSubTitle()
 * @method static \WpStarter\Wordpress\Admin\View\Action|null getAction()
 *
 */
class Layout extends Facade
{
    public static function getFacadeRoot()
    {
        if($route=Route::current()){
            return $route->layout();
        }
        return new \WpStarter\Wordpress\Admin\View\Layout();
    }
}
