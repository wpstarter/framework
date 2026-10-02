<?php

namespace WpStarter\Bus\Events;

use WpStarter\Bus\Batch;

class BatchCanceled
{
    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Bus\Batch  $batch  The batch instance.
     */
    public function __construct(
        public Batch $batch,
    ) {
    }
}
