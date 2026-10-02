<?php

namespace WpStarter\Foundation\Testing;

use WpStarter\Foundation\Application;
use WpStarter\Foundation\Support\Providers\RouteServiceProvider;

trait WithCachedRoutes
{
    /**
     * After creating the routes once, we can cache them for the remaining tests.
     *
     * @return void
     */
    protected function setUpWithCachedRoutes(): void
    {
        if ((CachedState::$cachedRoutes ?? null) === null) {
            $routes = $this->app['router']->getRoutes();

            $routes->refreshNameLookups();
            $routes->refreshActionLookups();

            CachedState::$cachedRoutes = $routes->compile();
        }

        $this->markRoutesCached($this->app);
    }

    /**
     * Reset the route service provider so it's not defaulting to loading cached routes.
     *
     * This is helpful if some of the tests in the suite apply this trait while others do not.
     *
     * @return void
     */
    protected function tearDownWithCachedRoutes(): void
    {
        RouteServiceProvider::loadCachedRoutesUsing(null);
    }

    /**
     * Inform the container to treat routes as cached.
     */
    protected function markRoutesCached(Application $app): void
    {
        $app->instance('routes.cached', true);

        RouteServiceProvider::loadCachedRoutesUsing(
            static fn () => ws_app('router')->setCompiledRoutes(CachedState::$cachedRoutes)
        );
    }
}
