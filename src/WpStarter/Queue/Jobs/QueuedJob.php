<?php

namespace WpStarter\Queue\Jobs;

class QueuedJob extends Job
{
    protected $id;
    protected $payload;
    public function __construct($connectionName, $id, $job, $payload, $queue, $delay)
    {
        $this->connectionName = $connectionName;
        $this->id = $id;
        $this->queue = $queue;
        $this->payload = $payload;
    }

    public function getJobId()
    {
        return $this->getJobId();
    }

    public function getRawBody()
    {
        return json_encode($this->payload);
    }
}
