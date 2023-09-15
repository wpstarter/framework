<?php

namespace WpStarter\Wordpress\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static boolean enqueue($styleOptions=[],$scriptOptions=[])
 * @method static boolean enqueueAdmin($styleOptions=[],$scriptOptions=[])
 * @method static mixed paginateLinks($args=[])
 */
class Livewire extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.livewire';
    }
}
