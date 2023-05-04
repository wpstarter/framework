<?php

namespace WpStarter\Wordpress\Facades;

use WpStarter\Support\Facades\Facade;

/**
 * @method static enqueue()
 */
class Livewire extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wp.livewire';
    }
}
