<?php

namespace WpStarter\Console\Events;

use WpStarter\Console\Scheduling\Event;

class ScheduledBackgroundTaskFinished
{
    /**
     * The scheduled event that ran.
     *
     * @var \WpStarter\Console\Scheduling\Event
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Console\Scheduling\Event  $task
     * @return void
     */
    public function __construct(Event $task)
    {
        $this->task = $task;
    }
}
