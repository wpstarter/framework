<?php

namespace WpStarter\Events;

use WpStarter\Support\Collection;

class InvokeQueuedClosure
{
    /**
     * Handle the event.
     *
     * @param  \WpStarter\SerializableClosure\SerializableClosure  $closure
     * @param  array  $arguments
     * @return void
     */
    public function handle($closure, array $arguments)
    {
        call_user_func($closure->getClosure(), ...$arguments);
    }

    /**
     * Handle a job failure.
     *
     * @param  \WpStarter\SerializableClosure\SerializableClosure  $closure
     * @param  array  $arguments
     * @param  array  $catchCallbacks
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed($closure, array $arguments, array $catchCallbacks, $exception)
    {
        $arguments[] = $exception;

        (new Collection($catchCallbacks))->each->__invoke(...$arguments);
    }
}
