<?php

namespace WpStarter\Log\Context;

use WpStarter\Contracts\Log\ContextLogProcessor as ContextLogProcessorContract;
use WpStarter\Queue\Events\JobProcessing;
use WpStarter\Queue\Queue;
use WpStarter\Support\Env;
use WpStarter\Support\Facades\Context;
use WpStarter\Support\ServiceProvider;

class ContextServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->scoped(Repository::class);

        if ($this->app->runningInConsole()) {
            $this->app->resolving(Repository::class, function (Repository $repository) {
                $context = Env::get('__LARAVEL_CONTEXT');

                if ($context && $context = json_decode($context, associative: true)) {
                    $repository->hydrate($context);
                }
            });
        }

        $this->app->bind(ContextLogProcessorContract::class, fn () => new ContextLogProcessor());
    }

    /**
     * Boot the application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::createPayloadUsing(function ($connection, $queue, $payload) {
            /** @phpstan-ignore staticMethod.notFound */
            $context = Context::dehydrate();

            return $context === null ? $payload : [
                ...$payload,
                'wpstarter:log:context' => $context,
            ];
        });

        $this->app['events']->listen(function (JobProcessing $event) {
            /** @phpstan-ignore staticMethod.notFound */
            Context::hydrate($event->job->payload()['wpstarter:log:context'] ?? null);
        });
    }
}
