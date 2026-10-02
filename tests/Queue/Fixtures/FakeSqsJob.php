<?php

namespace WpStarter\Tests\Queue\Fixtures;

use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Foundation\Queue\Queueable;

class FakeSqsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        //
    }
}
