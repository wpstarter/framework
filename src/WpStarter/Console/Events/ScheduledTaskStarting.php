<?php

namespace WpStarter\Console\Events;

use WpStarter\Console\Scheduling\Event;

class ScheduledTaskStarting
{
    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Console\Scheduling\Event  $task  The scheduled event being run.
     */
    public function __construct(
        public Event $task,
    ) {
    }
}
