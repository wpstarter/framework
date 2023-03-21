<?php

namespace WpStarter\Tests\Integration\Events;

use WpStarter\Events\CallQueuedListener;
use WpStarter\Events\InvokeQueuedClosure;
use function WpStarter\Events\queueable;
use WpStarter\Support\Facades\Bus;
use WpStarter\Support\Facades\Event;
use Orchestra\Testbench\TestCase;

class QueuedClosureListenerTest extends TestCase
{
    public function testAnonymousQueuedListenerIsQueued()
    {
        Bus::fake();

        Event::listen(queueable(function (TestEvent $event) {
            //
        })->catch(function (TestEvent $event) {
            //
        })->onConnection(null)->onQueue(null));

        Event::dispatch(new TestEvent);

        Bus::assertDispatched(CallQueuedListener::class, function ($job) {
            return $job->class == InvokeQueuedClosure::class;
        });
    }
}

class TestEvent
{
    //
}
