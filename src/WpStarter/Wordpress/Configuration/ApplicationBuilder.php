<?php

namespace WpStarter\Wordpress\Configuration;

use WpStarter\Contracts\Console\Kernel as ConsoleKernel;
use WpStarter\Contracts\Http\Kernel as HttpKernel;
use Closure;
use WpStarter\Wordpress\Routing\RoutingServiceProvider;
use WpStarter\Wordpress\Facades\Route as WpRoute;

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
    /**
     * Register the routing services for the application.
     *
     * @param  \Closure|null  $using
     * @param  array|string|null  $web
     * @param  array|string|null  $api
     * @param  string|null  $commands
     * @param  string|null  $channels
     * @param  string|null  $pages
     * @param  string  $apiPrefix
     * @param  callable|null  $then
     * @param  array|string|null $wp
     * @return $this
     */
    public function withRouting(
        ?Closure $using = null,
        array|string|null $web = null,
        array|string|null $api = null,
        ?string $commands = null,
        ?string $channels = null,
        ?string $pages = null,
        ?string $health = null,
        string $apiPrefix = 'api',
        ?callable $then = null,
        array|string|null $wp = null,
    ) {
        parent::withRouting(
            $using, $web, $api, $commands, $channels,
            $pages, $health, $apiPrefix, $then,
        );

        RoutingServiceProvider::loadRoutesUsing(
            function() use($wp){
                if (is_string($wp) || is_array($wp)) {
                    if (is_array($wp)) {
                        foreach ($wp as $wpRoute) {
                            if (realpath($wpRoute) !== false) {
                                WpRoute::middleware('web')->group($wpRoute);
                            }
                        }
                    } else {
                        WpRoute::middleware('web')->group($wp);
                    }
                }
            }
        );

        return $this;
    }

    /**
     * Register the global middleware, middleware groups, and middleware aliases for the application.
     *
     * @param  callable|null  $callback
     * @return $this
     */
    public function withMiddleware(?callable $callback = null)
    {
        $this->app->afterResolving(HttpKernel::class, function ($kernel) use ($callback) {
            $middleware = (new Middleware)
                ->redirectGuestsTo(fn () => ws_route('login'));

            if (! is_null($callback)) {
                $callback($middleware);
            }

            $this->pageMiddleware = $middleware->getPageMiddleware();
            $kernel->setGlobalMiddleware($middleware->getGlobalMiddleware());
            $kernel->setMiddlewareGroups($middleware->getMiddlewareGroups());
            $kernel->setMiddlewareAliases($middleware->getMiddlewareAliases());

            if ($priorities = $middleware->getMiddlewarePriority()) {
                $kernel->setMiddlewarePriority($priorities);
            }

            if ($priorityAppends = $middleware->getMiddlewarePriorityAppends()) {
                foreach ($priorityAppends as $newMiddleware => $after) {
                    $kernel->addToMiddlewarePriorityAfter($after, $newMiddleware);
                }
            }

            if ($priorityPrepends = $middleware->getMiddlewarePriorityPrepends()) {
                foreach ($priorityPrepends as $newMiddleware => $before) {
                    $kernel->addToMiddlewarePriorityBefore($before, $newMiddleware);
                }
            }
        });

        $this->app->afterResolving(ConsoleKernel::class, function () use ($callback) {
            if (! is_null($callback)) {
                $callback(new Middleware);
            }
        });

        return $this;
    }


}
