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
    protected static function getFacadeAccessor()
    {
        return 'wp.admin.layout';
    }
    /**
     * Resolve the facade root instance from the container.
     *
     * @param  string  $name
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if($route=Route::current()){
            return $route->layout();
        }
        if (! isset(static::$resolvedInstance[$name]) && ! isset(static::$app, static::$app[$name])) {
            $layout=new \WpStarter\Wordpress\Admin\View\Layout();
            $layout->setNoticeManager(static::$app['wp.admin.notice']);
            static::swap($layout);
        }

        return parent::resolveFacadeInstance($name);
    }
}
