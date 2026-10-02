<?php

namespace WpStarter\Console\Events;

use WpStarter\Console\Scheduling\Event;

class ScheduledBackgroundTaskFinished
{
    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Console\Scheduling\Event  $task  The scheduled event that ran.
     */
    public function __construct(
        public Event $task,
    ) {
    }
}
