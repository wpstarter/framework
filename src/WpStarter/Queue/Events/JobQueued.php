<?php

namespace WpStarter\Queue\Events;

use WpStarter\Queue\Jobs\QueuedJob;

class JobQueued
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The job instance.
     *
     * @var \WpStarter\Contracts\Queue\Job
     */
    public $job;


    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  string|int|null  $id
     * @param  \Closure|string|object  $job
     * @return void
     */
    public function __construct($connectionName, $id, $job, $payload, $queue, $delay)
    {
        $this->connectionName = $connectionName;
        $this->job = new QueuedJob($connectionName, $id, $job, $payload, $queue, $delay);
    }
}
