<?php

namespace WpStarter\Foundation\Bus;

use Closure;
use WpStarter\Contracts\Bus\Dispatcher;
use WpStarter\Support\Fluent;

trait Dispatchable
{
    /**
     * Dispatch the job with the given arguments.
     *
     * @param  mixed  ...$arguments
     * @return \WpStarter\Foundation\Bus\PendingDispatch
     */
    public static function dispatch(...$arguments)
    {
        return static::newPendingDispatch(new static(...$arguments));
    }

    /**
     * Dispatch the job with the given arguments if the given truth test passes.
     *
     * @param  bool|\Closure  $boolean
     * @param  mixed  ...$arguments
     * @return \WpStarter\Foundation\Bus\PendingDispatch|\WpStarter\Support\Fluent
     */
    public static function dispatchIf($boolean, ...$arguments)
    {
        if ($boolean instanceof Closure) {
            $dispatchable = new static(...$arguments);

            return ws_value($boolean, $dispatchable)
                ? static::newPendingDispatch($dispatchable)
                : new Fluent;
        }

        return ws_value($boolean)
            ? static::newPendingDispatch(new static(...$arguments))
            : new Fluent;
    }

    /**
     * Dispatch the job with the given arguments unless the given truth test passes.
     *
     * @param  bool|\Closure  $boolean
     * @param  mixed  ...$arguments
     * @return \WpStarter\Foundation\Bus\PendingDispatch|\WpStarter\Support\Fluent
     */
    public static function dispatchUnless($boolean, ...$arguments)
    {
        if ($boolean instanceof Closure) {
            $dispatchable = new static(...$arguments);

            return ! ws_value($boolean, $dispatchable)
                ? static::newPendingDispatch($dispatchable)
                : new Fluent;
        }

        return ! ws_value($boolean)
            ? static::newPendingDispatch(new static(...$arguments))
            : new Fluent;
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @param  mixed  ...$arguments
     * @return mixed
     */
    public static function dispatchSync(...$arguments)
    {
        return app(Dispatcher::class)->dispatchSync(new static(...$arguments));
    }

    /**
     * Dispatch a command to its appropriate handler after the current process.
     *
     * @param  mixed  ...$arguments
     * @return mixed
     */
    public static function dispatchAfterResponse(...$arguments)
    {
        return self::dispatch(...$arguments)->afterResponse();
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param  array  $chain
     * @return \WpStarter\Foundation\Bus\PendingChain
     */
    public static function withChain($chain)
    {
        return new PendingChain(static::class, $chain);
    }

    /**
     * Create a new pending job dispatch instance.
     *
     * @param  mixed  $job
     * @return \WpStarter\Foundation\Bus\PendingDispatch
     */
    protected static function newPendingDispatch($job)
    {
        return new PendingDispatch($job);
    }
}
