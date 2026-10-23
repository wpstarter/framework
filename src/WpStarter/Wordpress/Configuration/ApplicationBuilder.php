<?php

namespace WpStarter\Wordpress\Configuration;
class ApplicationBuilder extends \WpStarter\Foundation\Configuration\ApplicationBuilder
{
    /**
     * Register the standard kernel classes for the application.
     *
     * @return $this
     */
    public function withKernels()
    {
        $this->app->singleton(
            \WpStarter\Contracts\Http\Kernel::class,
            \WpStarter\Wordpress\Http\Kernel::class,
        );

        $this->app->singleton(
            \WpStarter\Contracts\Console\Kernel::class,
            \WpStarter\Wordpress\Console\Kernel::class,
        );

        return $this;
    }


}
