<?php

namespace WpStarter\Support\Facades;

use WpStarter\Queue\Worker;
use WpStarter\Support\Testing\Fakes\QueueFake;

/**
 * @method static \WpStarter\Contracts\Queue\Job|null pop(string $queue = null)
 * @method static \WpStarter\Contracts\Queue\Queue setConnectionName(string $name)
 * @method static int size(string $queue = null)
 * @method static mixed bulk(array $jobs, mixed $data = '', string $queue = null)
 * @method static mixed later(\DateTimeInterface|\DateInterval|int $delay, string|object $job, mixed $data = '', string $queue = null)
 * @method static mixed laterOn(string $queue, \DateTimeInterface|\DateInterval|int $delay, string|object $job, mixed $data = '')
 * @method static mixed push(string|object $job, mixed $data = '', $queue = null)
 * @method static mixed pushOn(string $queue, string|object $job, mixed $data = '')
 * @method static mixed pushRaw(string $payload, string $queue = null, array $options = [])
 * @method static string getConnectionName()
 * @method static void assertNotPushed(string|\Closure $job, callable $callback = null)
 * @method static void assertNothingPushed()
 * @method static void assertPushed(string|\Closure $job, callable|int $callback = null)
 * @method static void assertPushedOn(string $queue, string|\Closure $job, callable $callback = null)
 * @method static void assertPushedWithChain(string $job, array $expectedChain = [], callable $callback = null)
 *
 * @see \WpStarter\Queue\QueueManager
 * @see \WpStarter\Queue\Queue
 */
class Queue extends Facade
{
    /**
     * Register a callback to be executed to pick jobs.
     *
     * @param  string  $workerName
     * @param  callable  $callback
     * @return void
     */
    public static function popUsing($workerName, $callback)
    {
        return Worker::popUsing($workerName, $callback);
    }

    /**
     * Replace the bound instance with a fake.
     *
     * @return \WpStarter\Support\Testing\Fakes\QueueFake
     */
    public static function fake()
    {
        static::swap($fake = new QueueFake(static::getFacadeApplication()));

        return $fake;
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'queue';
    }
}
