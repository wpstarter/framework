<?php

namespace WpStarter\Foundation\Queue;

use WpStarter\Bus\Queueable as QueueableByBus;
use WpStarter\Foundation\Bus\Dispatchable;
use WpStarter\Queue\InteractsWithQueue;
use WpStarter\Queue\SerializesModels;

trait Queueable
{
    use Dispatchable, InteractsWithQueue, QueueableByBus, SerializesModels;
}
