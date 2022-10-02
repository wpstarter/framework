<?php

namespace WpStarter\Queue\Events;

class JobExceptionOccurred
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
     * The exception instance.
     *
     * @var \Throwable
     */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  \WpStarter\Contracts\Queue\Job  $job
     * @param  \Throwable  $exception
     * @return void
     */
    public function __construct($connectionName, $job, $exception)
    {
        $this->job = $job;
        $this->exception = $exception;
        $this->connectionName = $connectionName;
    }
}
