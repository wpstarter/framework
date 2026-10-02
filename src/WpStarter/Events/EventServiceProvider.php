<?php

namespace WpStarter\Events;

use WpStarter\Contracts\Queue\Factory as QueueFactoryContract;
use WpStarter\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('events', function ($app) {
            return (new Dispatcher($app))->setQueueResolver(function () {
                return ws_app(QueueFactoryContract::class);
            })->setTransactionManagerResolver(function () {
                return ws_app()->bound('db.transactions')
                    ? ws_app('db.transactions')
                    : null;
            });
        });
    }
}
