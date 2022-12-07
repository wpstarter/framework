<?php

namespace WpStarter\Wordpress\Admin\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static \WpStarter\Wordpress\Admin\Notice\Message notify($message, $type)
 *
 * @method static \WpStarter\Wordpress\Admin\Notice\Message error($message)
 * @method static \WpStarter\Wordpress\Admin\Notice\Message warning($message)
 * @method static \WpStarter\Wordpress\Admin\Notice\Message success($message)
 * @method static \WpStarter\Wordpress\Admin\Notice\Message info($message)
 *
 * @method static \WpStarter\Wordpress\Admin\Notice\NoticeManager addNotice(\WpStarter\Wordpress\Admin\Notice\Message $notice)
 */
class Notice extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.admin.notice';
    }
}