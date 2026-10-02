<?php

namespace WpStarter\Foundation\Exceptions;

use WpStarter\Support\Collection;
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
        View::replaceNamespace('errors', (new Collection(ws_config('view.paths')))
            ->map(fn ($path) => "{$path}/errors")
            ->push(__DIR__.'/views')
            ->all()
        );
    }
}
