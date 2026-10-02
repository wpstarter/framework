<?php

namespace WpStarter\Tests\Queue\Fixtures;

use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Foundation\Queue\Queueable;

class FakeSqsJobWithMessageGroup implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        //
    }

    /**
     * Message group method called by SqsQueue.
     *
     * @return string
     */
    public function messageGroup(): string
    {
        return 'group-1';
    }
}
