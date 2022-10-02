<?php

namespace WpStarter\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \WpStarter\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
