<?php

namespace WpStarter\Broadcasting;

use WpStarter\Contracts\Broadcasting\HasBroadcastChannel;
use Stringable;

class Channel implements Stringable
{
    /**
     * The channel's name.
     *
     * @var string
     */
    public $name;

    /**
     * Create a new channel instance.
     *
     * @param  \WpStarter\Contracts\Broadcasting\HasBroadcastChannel|string  $name
     */
    public function __construct($name)
    {
        $this->name = $name instanceof HasBroadcastChannel ? $name->broadcastChannel() : $name;
    }

    /**
     * Convert the channel instance to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
