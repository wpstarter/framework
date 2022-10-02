<?php

namespace WpStarter\Contracts\Broadcasting;

interface ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \WpStarter\Broadcasting\Channel|\WpStarter\Broadcasting\Channel[]|string[]|string
     */
    public function broadcastOn();
}
