<?php

namespace WpStarter\Notifications;

trait HasDatabaseNotifications
{
    /**
     * Get the entity's notifications.
     *
     * @return \WpStarter\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    /**
     * Get the entity's read notifications.
     *
     * @return \WpStarter\Database\Query\Builder
     */
    public function readNotifications()
    {
        return $this->notifications()->read();
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return \WpStarter\Database\Query\Builder
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }
}
