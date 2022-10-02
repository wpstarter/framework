<?php

namespace WpStarter\Console\Events;

class ArtisanStarting
{
    /**
     * The Artisan application instance.
     *
     * @var \WpStarter\Console\Application
     */
    public $artisan;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Console\Application  $artisan
     * @return void
     */
    public function __construct($artisan)
    {
        $this->artisan = $artisan;
    }
}
