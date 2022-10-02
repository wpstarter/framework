<?php

namespace WpStarter\Foundation\Exceptions;

use WpStarter\Support\Facades\View;

class RegisterErrorViewPaths
{
    /**
     * Register the error view paths.
     *
     * @return void
     */
    public function __invoke()
    {
        View::replaceNamespace('errors', ws_collect(ws_config('view.paths'))->map(function ($path) {
            return "{$path}/errors";
        })->push(__DIR__.'/views')->all());
    }
}
