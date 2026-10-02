<?php

namespace WpStarter\Tests\Integration\Support;

use WpStarter\Bus\Queueable;
use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Foundation\Bus\Dispatchable;
use WpStarter\Foundation\Http\Middleware\InvokeDeferredCallbacks;
use WpStarter\Support\Facades\Route;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;

class DeferredCallbackTest extends TestCase
{
    #[WithConfig('queue.default', 'sync')]
    public function test_deferred_callback_is_not_discarded_by_sync_job()
    {
        $executed = false;

        Route::get('/test', function () use (&$executed) {
            defer(function () use (&$executed) {
                $executed = true;
            });

            dispatch(new TestSyncJob);
        })->middleware(InvokeDeferredCallbacks::class);

        $this->get('/test');

        $this->assertTrue($executed);
    }
}

class TestSyncJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        //
    }
}
