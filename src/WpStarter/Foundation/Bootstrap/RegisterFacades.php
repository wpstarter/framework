<?php

namespace WpStarter\Foundation\Bootstrap;

use WpStarter\Contracts\Foundation\Application;
use WpStarter\Foundation\AliasLoader;
use WpStarter\Foundation\PackageManifest;
use WpStarter\Support\Facades\Facade;

class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param  \WpStarter\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication($app);

        AliasLoader::getInstance(array_merge(
            $app->make('config')->get('app.aliases', []),
            $app->make(PackageManifest::class)->aliases()
        ))->register();
    }
}
