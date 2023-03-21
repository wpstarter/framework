<?php

namespace WpStarter\Tests\Queue;

use Exception;
use WpStarter\Container\Container;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Contracts\Queue\QueueableEntity;
use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Queue\InteractsWithQueue;
use WpStarter\Queue\Jobs\SyncJob;
use WpStarter\Queue\SyncQueue;
use LogicException;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class QueueSyncQueueTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();

        Container::setInstance(null);
    }

    public function testPushShouldFireJobInstantly()
    {
        unset($_SERVER['__sync.test']);

        $sync = new SyncQueue;
        $container = new Container;
        $sync->setContainer($container);

        $sync->push(SyncQueueTestHandler::class, ['foo' => 'bar']);
        $this->assertInstanceOf(SyncJob::class, $_SERVER['__sync.test'][0]);
        $this->assertEquals(['foo' => 'bar'], $_SERVER['__sync.test'][1]);
    }

    public function testFailedJobGetsHandledWhenAnExceptionIsThrown()
    {
        unset($_SERVER['__sync.failed']);

        $sync = new SyncQueue;
        $container = new Container;
        Container::setInstance($container);
        $events = m::mock(Dispatcher::class);
        $events->shouldReceive('dispatch')->times(3);
        $container->instance('events', $events);
        $container->instance(Dispatcher::class, $events);
        $sync->setContainer($container);

        try {
            $sync->push(FailingSyncQueueTestHandler::class, ['foo' => 'bar']);
        } catch (Exception $e) {
            $this->assertTrue($_SERVER['__sync.failed']);
        }

        Container::setInstance();
    }

    public function testCreatesPayloadObject()
    {
        $sync = new SyncQueue;
        $container = new Container;
        $container->bind(\WpStarter\Contracts\Events\Dispatcher::class, \WpStarter\Events\Dispatcher::class);
        $container->bind(\WpStarter\Contracts\Bus\Dispatcher::class, \WpStarter\Bus\Dispatcher::class);
        $container->bind(\WpStarter\Contracts\Container\Container::class, \WpStarter\Container\Container::class);
        $sync->setContainer($container);

        SyncQueue::createPayloadUsing(function ($connection, $queue, $payload) {
            return ['data' => ['extra' => 'extraValue']];
        });

        try {
            $sync->push(new SyncQueueJob());
        } catch (LogicException $e) {
            $this->assertEquals('extraValue', $e->getMessage());
        }
    }
}

class SyncQueueTestEntity implements QueueableEntity
{
    public function getQueueableId()
    {
        return 1;
    }

    public function getQueueableConnection()
    {
        //
    }

    public function getQueueableRelations()
    {
        //
    }
}

class SyncQueueTestHandler
{
    public function fire($job, $data)
    {
        $_SERVER['__sync.test'] = func_get_args();
    }
}

class FailingSyncQueueTestHandler
{
    public function fire($job, $data)
    {
        throw new Exception;
    }

    public function failed()
    {
        $_SERVER['__sync.failed'] = true;
    }
}

class SyncQueueJob implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle()
    {
        throw new LogicException($this->getValueFromJob('extra'));
    }

    public function getValueFromJob($key)
    {
        $payload = $this->job->payload();

        return $payload['data'][$key] ?? null;
    }
}
