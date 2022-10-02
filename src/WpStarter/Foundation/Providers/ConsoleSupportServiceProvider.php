<?php

namespace WpStarter\Foundation\Providers;

use WpStarter\Contracts\Support\DeferrableProvider;
use WpStarter\Database\MigrationServiceProvider;
use WpStarter\Support\AggregateServiceProvider;

class ConsoleSupportServiceProvider extends AggregateServiceProvider implements DeferrableProvider
{
    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [
        ArtisanServiceProvider::class,
        MigrationServiceProvider::class,
        ComposerServiceProvider::class,
    ];
}
