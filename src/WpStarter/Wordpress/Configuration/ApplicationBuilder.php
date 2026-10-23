<?php

namespace WpStarter\Wordpress\Configuration;
use WpStarter\Contracts\Console\Kernel as ConsoleKernel;
use WpStarter\Contracts\Http\Kernel as HttpKernel;

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
