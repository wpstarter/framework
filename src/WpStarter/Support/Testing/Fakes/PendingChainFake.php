<?php

namespace WpStarter\Support\Testing\Fakes;

use Closure;
use WpStarter\Foundation\Bus\PendingChain;
use WpStarter\Queue\CallQueuedClosure;

class PendingChainFake extends PendingChain
{
    /**
     * The fake bus instance.
     *
     * @var \WpStarter\Support\Testing\Fakes\BusFake
     */
    protected $bus;

    /**
     * Create a new pending chain instance.
     *
     * @param  \WpStarter\Support\Testing\Fakes\BusFake  $bus
     * @param  mixed  $job
     * @param  array  $chain
     * @return void
     */
    public function __construct(BusFake $bus, $job, $chain)
    {
        $this->bus = $bus;
        $this->job = $job;
        $this->chain = $chain;
    }

    /**
     * Dispatch the job with the given arguments.
     *
     * @return \WpStarter\Foundation\Bus\PendingDispatch
     */
    public function dispatch()
    {
        if (is_string($this->job)) {
            $firstJob = new $this->job(...func_get_args());
        } elseif ($this->job instanceof Closure) {
            $firstJob = CallQueuedClosure::create($this->job);
        } else {
            $firstJob = $this->job;
        }

        $firstJob->allOnConnection($this->connection);
        $firstJob->allOnQueue($this->queue);
        $firstJob->chain($this->chain);
        $firstJob->delay($this->delay);
        $firstJob->chainCatchCallbacks = $this->catchCallbacks();

        return $this->bus->dispatch($firstJob);
    }
}
