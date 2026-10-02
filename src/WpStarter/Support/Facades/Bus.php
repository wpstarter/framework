<?php

namespace WpStarter\Support\Facades;

use WpStarter\Bus\BatchRepository;
use WpStarter\Contracts\Bus\Dispatcher as BusDispatcherContract;
use WpStarter\Foundation\Bus\PendingChain;
use WpStarter\Support\Testing\Fakes\BusFake;

/**
 * @method static mixed dispatch(mixed $command)
 * @method static mixed dispatchSync(mixed $command, mixed $handler = null)
 * @method static mixed dispatchNow(mixed $command, mixed $handler = null)
 * @method static \WpStarter\Bus\Batch|null findBatch(string $batchId)
 * @method static \WpStarter\Bus\PendingBatch batch(\WpStarter\Support\Collection|mixed $jobs)
 * @method static \WpStarter\Foundation\Bus\PendingChain chain(\WpStarter\Support\Collection|array|null $jobs = null)
 * @method static bool hasCommandHandler(mixed $command)
 * @method static mixed getCommandHandler(mixed $command)
 * @method static mixed dispatchToQueue(mixed $command)
 * @method static void dispatchAfterResponse(mixed $command, mixed $handler = null)
 * @method static \WpStarter\Bus\Dispatcher pipeThrough(array $pipes)
 * @method static \WpStarter\Bus\Dispatcher map(array $map)
 * @method static \WpStarter\Bus\Dispatcher withDispatchingAfterResponses()
 * @method static \WpStarter\Bus\Dispatcher withoutDispatchingAfterResponses()
 * @method static \WpStarter\Support\Testing\Fakes\BusFake except(array|string $jobsToDispatch)
 * @method static void assertDispatched(string|\Closure $command, callable|int|null $callback = null)
 * @method static void assertDispatchedOnce(string|\Closure $command)
 * @method static void assertDispatchedTimes(string|\Closure $command, int $times = 1)
 * @method static void assertNotDispatched(string|\Closure $command, callable|null $callback = null)
 * @method static void assertNothingDispatched()
 * @method static void assertDispatchedSync(string|\Closure $command, callable|int|null $callback = null)
 * @method static void assertDispatchedSyncTimes(string|\Closure $command, int $times = 1)
 * @method static void assertNotDispatchedSync(string|\Closure $command, callable|null $callback = null)
 * @method static void assertDispatchedAfterResponse(string|\Closure $command, callable|int|null $callback = null)
 * @method static void assertDispatchedAfterResponseTimes(string|\Closure $command, int $times = 1)
 * @method static void assertNotDispatchedAfterResponse(string|\Closure $command, callable|null $callback = null)
 * @method static void assertChained(array $expectedChain)
 * @method static void assertNothingChained()
 * @method static void assertDispatchedWithoutChain(string|\Closure $command, callable|null $callback = null)
 * @method static \WpStarter\Support\Testing\Fakes\ChainedBatchTruthTest chainedBatch(\Closure $callback)
 * @method static void assertBatched(array|callable $callback)
 * @method static void assertBatchCount(int $count)
 * @method static void assertNothingBatched()
 * @method static void assertNothingPlaced()
 * @method static \WpStarter\Support\Collection dispatched(string $command, callable|null $callback = null)
 * @method static \WpStarter\Support\Collection dispatchedSync(string $command, callable|null $callback = null)
 * @method static \WpStarter\Support\Collection dispatchedAfterResponse(string $command, callable|null $callback = null)
 * @method static \WpStarter\Support\Collection batched(callable $callback)
 * @method static bool hasDispatched(string $command)
 * @method static bool hasDispatchedSync(string $command)
 * @method static bool hasDispatchedAfterResponse(string $command)
 * @method static \WpStarter\Bus\Batch dispatchFakeBatch(string $name = '')
 * @method static \WpStarter\Bus\Batch recordPendingBatch(\WpStarter\Bus\PendingBatch $pendingBatch)
 * @method static \WpStarter\Support\Testing\Fakes\BusFake serializeAndRestore(bool $serializeAndRestore = true)
 * @method static array dispatchedBatches()
 *
 * @see \WpStarter\Bus\Dispatcher
 * @see \WpStarter\Support\Testing\Fakes\BusFake
 */
class Bus extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @param  array|string  $jobsToFake
     * @param  \WpStarter\Bus\BatchRepository|null  $batchRepository
     * @return \WpStarter\Support\Testing\Fakes\BusFake
     */
    public static function fake($jobsToFake = [], ?BatchRepository $batchRepository = null)
    {
        $actualDispatcher = static::isFake()
            ? static::getFacadeRoot()->dispatcher
            : static::getFacadeRoot();

        return ws_tap(new BusFake($actualDispatcher, $jobsToFake, $batchRepository), function ($fake) {
            static::swap($fake);
        });
    }

    /**
     * Dispatch the given chain of jobs.
     *
     * @param  mixed  $jobs
     * @return \WpStarter\Foundation\Bus\PendingDispatch
     */
    public static function dispatchChain($jobs)
    {
        $jobs = is_array($jobs) ? $jobs : func_get_args();

        return (new PendingChain(array_shift($jobs), $jobs))
            ->dispatch();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BusDispatcherContract::class;
    }
}
