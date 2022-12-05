<?php

namespace WpStarter\Wordpress\Admin\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static \WpStarter\Wordpress\Admin\Notice\Notice notify($message,$type)
 *
 * @method static \WpStarter\Wordpress\Admin\Notice\Notice error($message)
 * @method static \WpStarter\Wordpress\Admin\Notice\Notice warning($message)
 * @method static \WpStarter\Wordpress\Admin\Notice\Notice success($message)
 * @method static \WpStarter\Wordpress\Admin\Notice\Notice info($message)
 *
 * @method static \WpStarter\Wordpress\Admin\Notice\NoticeManager addNotice(\WpStarter\Wordpress\Admin\Notice\Notice $notice)
 */
class Notice extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.admin.notice';
    }
}