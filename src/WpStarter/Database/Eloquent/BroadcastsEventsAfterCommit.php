<?php

namespace WpStarter\Database\Eloquent;

trait BroadcastsEventsAfterCommit
{
    use BroadcastsEvents;

    /**
     * Determine if the model event broadcast queued job should be dispatched after all transactions are committed.
     *
     * @return bool
     */
    public function broadcastAfterCommit()
    {
        return true;
    }
}
