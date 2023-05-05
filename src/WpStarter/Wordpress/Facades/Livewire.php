<?php

namespace WpStarter\Wordpress\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static boolean enqueue($styleOptions=[],$scriptOptions=[])
 */
class Livewire extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.livewire';
    }
}
