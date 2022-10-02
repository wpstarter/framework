<?php

namespace WpStarter\Console\Events;

use WpStarter\Console\Scheduling\Event;

class ScheduledTaskFinished
{
    /**
     * The scheduled event that ran.
     *
     * @var \WpStarter\Console\Scheduling\Event
     */
    public $task;

    /**
     * The runtime of the scheduled event.
     *
     * @var float
     */
    public $runtime;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Console\Scheduling\Event  $task
     * @param  float  $runtime
     * @return void
     */
    public function __construct(Event $task, $runtime)
    {
        $this->task = $task;
        $this->runtime = $runtime;
    }
}
