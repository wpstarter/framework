<?php

namespace WpStarter\Support\Testing\Fakes;

use Closure;

class ChainedBatchTruthTest
{
    /**
     * The underlying truth test.
     *
     * @var \Closure(\WpStarter\Bus\PendingBatch): bool
     */
    protected $callback;

    /**
     * Create a new truth test instance.
     *
     * @param  \Closure(\WpStarter\Bus\PendingBatch): bool  $callback
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Invoke the truth test with the given pending batch.
     *
     * @param  \WpStarter\Bus\PendingBatch  $pendingBatch
     * @return bool
     */
    public function __invoke($pendingBatch)
    {
        return call_user_func($this->callback, $pendingBatch);
    }
}
