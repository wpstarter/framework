<?php

namespace WpStarter\Tests\Events;

use WpStarter\Container\Container;
use WpStarter\Contracts\Queue\Queue;
use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Events\CallQueuedListener;
use WpStarter\Events\Dispatcher;
use WpStarter\Support\Testing\Fakes\QueueFake;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class QueuedEventsTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testQueuedEventHandlersAreQueued()
    {
        $d = new Dispatcher;
        $queue = m::mock(Queue::class);

        $queue->shouldReceive('connection')->once()->with(null)->andReturnSelf();

        $queue->shouldReceive('pushOn')->once()->with(null, m::type(CallQueuedListener::class));

        $d->setQueueResolver(function () use ($queue) {
            return $queue;
        });

        $d->listen('some.event', TestDispatcherQueuedHandler::class.'@someMethod');
        $d->dispatch('some.event', ['foo', 'bar']);
    }

    public function testCustomizedQueuedEventHandlersAreQueued()
    {
        $d = new Dispatcher;

        $fakeQueue = new QueueFake(new Container);

        $d->setQueueResolver(function () use ($fakeQueue) {
            return $fakeQueue;
        });

        $d->listen('some.event', TestDispatcherConnectionQueuedHandler::class.'@handle');
        $d->dispatch('some.event', ['foo', 'bar']);

        $fakeQueue->assertPushedOn('my_queue', CallQueuedListener::class);
    }

    public function testQueueIsSetByGetQueue()
    {
        $d = new Dispatcher;

        $fakeQueue = new QueueFake(new Container);

        $d->setQueueResolver(function () use ($fakeQueue) {
            return $fakeQueue;
        });

        $d->listen('some.event', TestDispatcherGetQueue::class.'@handle');
        $d->dispatch('some.event', ['foo', 'bar']);

        $fakeQueue->assertPushedOn('some_other_queue', CallQueuedListener::class);
    }

    public function testQueueIsSetByGetConnection()
    {
        $d = new Dispatcher;
        $queue = m::mock(Queue::class);

        $queue->shouldReceive('connection')->once()->with('some_other_connection')->andReturnSelf();

        $queue->shouldReceive('pushOn')->once()->with(null, m::type(CallQueuedListener::class));

        $d->setQueueResolver(function () use ($queue) {
            return $queue;
        });

        $d->listen('some.event', TestDispatcherGetConnection::class.'@handle');
        $d->dispatch('some.event', ['foo', 'bar']);
    }

    public function testQueuePropagateRetryUntilAndMaxExceptions()
    {
        $d = new Dispatcher;

        $fakeQueue = new QueueFake(new Container);

        $d->setQueueResolver(function () use ($fakeQueue) {
            return $fakeQueue;
        });

        $d->listen('some.event', TestDispatcherOptions::class.'@handle');
        $d->dispatch('some.event', ['foo', 'bar']);

        $fakeQueue->assertPushed(CallQueuedListener::class, function ($job) {
            return $job->maxExceptions === 1 && $job->retryUntil !== null;
        });
    }

    public function testQueuePropagateMiddleware()
    {
        $d = new Dispatcher;

        $fakeQueue = new QueueFake(new Container);

        $d->setQueueResolver(function () use ($fakeQueue) {
            return $fakeQueue;
        });

        $d->listen('some.event', TestDispatcherMiddleware::class.'@handle');
        $d->dispatch('some.event', ['foo', 'bar']);

        $fakeQueue->assertPushed(CallQueuedListener::class, function ($job) {
            return count($job->middleware) === 1 && $job->middleware[0] instanceof TestMiddleware;
        });
    }
}

class TestDispatcherQueuedHandler implements ShouldQueue
{
    public function handle()
    {
        //
    }
}

class TestDispatcherConnectionQueuedHandler implements ShouldQueue
{
    public $connection = 'redis';

    public $delay = 10;

    public $queue = 'my_queue';

    public function handle()
    {
        //
    }
}

class TestDispatcherGetQueue implements ShouldQueue
{
    public $queue = 'my_queue';

    public function handle()
    {
        //
    }

    public function viaQueue()
    {
        return 'some_other_queue';
    }
}

class TestDispatcherGetConnection implements ShouldQueue
{
    public $connection = 'my_connection';

    public function handle()
    {
        //
    }

    public function viaConnection()
    {
        return 'some_other_connection';
    }
}

class TestDispatcherOptions implements ShouldQueue
{
    public $maxExceptions = 1;

    public function retryUntil()
    {
        return ws_now()->addHour(1);
    }

    public function handle()
    {
        //
    }
}

class TestDispatcherMiddleware implements ShouldQueue
{
    public function middleware()
    {
        return [new TestMiddleware()];
    }

    public function handle()
    {
        //
    }
}

class TestMiddleware
{
    public function handle($job, $next)
    {
        $next($job);
    }
}
