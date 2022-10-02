<?php

namespace WpStarter\Notifications;

use WpStarter\Contracts\Notifications\Dispatcher;
use WpStarter\Support\Str;

trait RoutesNotifications
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $instance
     * @return void
     */
    public function notify($instance)
    {
        ws_app(Dispatcher::class)->send($this, $instance);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  mixed  $instance
     * @param  array|null  $channels
     * @return void
     */
    public function notifyNow($instance, array $channels = null)
    {
        ws_app(Dispatcher::class)->sendNow($this, $instance, $channels);
    }

    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @param  \WpStarter\Notifications\Notification|null  $notification
     * @return mixed
     */
    public function routeNotificationFor($driver, $notification = null)
    {
        if (method_exists($this, $method = 'routeNotificationFor'.Str::studly($driver))) {
            return $this->{$method}($notification);
        }

        switch ($driver) {
            case 'database':
                return $this->notifications();
            case 'mail':
                return $this->email;
        }
    }
}
