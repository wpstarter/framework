<?php

namespace WpStarter\Notifications;

use WpStarter\Database\Eloquent\Collection;

class DatabaseNotificationCollection extends Collection
{
    /**
     * Mark all notifications as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        $this->each->markAsRead();
    }

    /**
     * Mark all notifications as unread.
     *
     * @return void
     */
    public function markAsUnread()
    {
        $this->each->markAsUnread();
    }
}
