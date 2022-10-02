<?php

namespace WpStarter\Bus\Events;

use WpStarter\Bus\Batch;

class BatchDispatched
{
    /**
     * The batch instance.
     *
     * @var \WpStarter\Bus\Batch
     */
    public $batch;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Bus\Batch  $batch
     * @return void
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }
}
