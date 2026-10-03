<?php

namespace WpStarter\Console\Concerns;

use WpStarter\Support\Collection;
use Symfony\Component\Finder\Finder;

trait FindsAvailableModels
{
    /**
     * Get a list of possible model names.
     *
     * @return array<int, string>
     */
    protected function findAvailableModels()
    {
        $modelPath = is_dir(ws_app_path('Models')) ? ws_app_path('Models') : ws_app_path();

        return (new Collection(Finder::create()->files()->depth(0)->in($modelPath)))
            ->map(fn ($file) => $file->getBasename('.php'))
            ->sort()
            ->values()
            ->all();
    }
}
