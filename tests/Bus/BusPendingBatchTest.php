<?php

namespace WpStarter\Tests\Bus;

use WpStarter\Bus\Batch;
use WpStarter\Bus\Batchable;
use WpStarter\Bus\BatchRepository;
use WpStarter\Bus\PendingBatch;
use WpStarter\Container\Container;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Support\Collection;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

class BusPendingBatchTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function test_pending_batch_may_be_configured_and_dispatched()
    {
        $container = new Container;

        $eventDispatcher = m::mock(Dispatcher::class);
        $eventDispatcher->shouldReceive('dispatch')->once();

        $container->instance(Dispatcher::class, $eventDispatcher);

        $job = new class
        {
            use Batchable;
        };

        $pendingBatch = new PendingBatch($container, new Collection([$job]));

        $pendingBatch = $pendingBatch->then(function () {
            //
        })->catch(function () {
            //
        })->allowFailures()->onConnection('test-connection')->onQueue('test-queue')->withOption('extra-option', 123);

        $this->assertSame('test-connection', $pendingBatch->connection());
        $this->assertSame('test-queue', $pendingBatch->queue());
        $this->assertCount(1, $pendingBatch->thenCallbacks());
        $this->assertCount(1, $pendingBatch->catchCallbacks());
        $this->assertArrayHasKey('extra-option', $pendingBatch->options);
        $this->assertSame(123, $pendingBatch->options['extra-option']);

        $repository = m::mock(BatchRepository::class);
        $repository->shouldReceive('store')->once()->with($pendingBatch)->andReturn($batch = m::mock(stdClass::class));
        $batch->shouldReceive('add')->once()->with(m::type(Collection::class))->andReturn($batch = m::mock(Batch::class));

        $container->instance(BatchRepository::class, $repository);

        $pendingBatch->dispatch();
    }

    public function test_batch_is_deleted_from_storage_if_exception_thrown_during_batching()
    {
        $this->expectException(RuntimeException::class);

        $container = new Container;

        $job = new class
        {
        };

        $pendingBatch = new PendingBatch($container, new Collection([$job]));

        $repository = m::mock(BatchRepository::class);

        $repository->shouldReceive('store')->once()->with($pendingBatch)->andReturn($batch = m::mock(stdClass::class));

        $batch->id = 'test-id';

        $batch->shouldReceive('add')->once()->andReturnUsing(function () {
            throw new RuntimeException('Failed to add jobs...');
        });

        $repository->shouldReceive('delete')->once()->with('test-id');

        $container->instance(BatchRepository::class, $repository);

        $pendingBatch->dispatch();
    }
}
