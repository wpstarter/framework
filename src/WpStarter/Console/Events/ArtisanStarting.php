<?php

namespace WpStarter\Console\Events;

use WpStarter\Console\Application;

class ArtisanStarting
{
    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Console\Application  $artisan  The Artisan application instance.
     */
    public function __construct(
        public Application $artisan,
    ) {
    }
}
