<?php

namespace WpStarter\Foundation\Bootstrap;

use WpStarter\Contracts\Foundation\Application;

class BootProviders
{
    /**
     * Bootstrap the given application.
     *
     * @param  \WpStarter\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $app->boot();
    }
}
