<?php

namespace WpStarter\Notifications\Channels;

use WpStarter\Notifications\Notification;
use RuntimeException;

class DatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \WpStarter\Notifications\Notification  $notification
     * @return \WpStarter\Database\Eloquent\Model
     */
    public function send($notifiable, Notification $notification)
    {
        return $notifiable->routeNotificationFor('database', $notification)->create(
            $this->buildPayload($notifiable, $notification)
        );
    }

    /**
     * Build an array payload for the DatabaseNotification Model.
     *
     * @param  mixed  $notifiable
     * @param  \WpStarter\Notifications\Notification  $notification
     * @return array
     */
    protected function buildPayload($notifiable, Notification $notification)
    {
        return [
            'id' => $notification->id,
            'type' => method_exists($notification, 'databaseType')
                        ? $notification->databaseType($notifiable)
                        : get_class($notification),
            'data' => $this->getData($notifiable, $notification),
            'read_at' => null,
        ];
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @param  \WpStarter\Notifications\Notification  $notification
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toDatabase')) {
            return is_array($data = $notification->toDatabase($notifiable))
                                ? $data : $data->data;
        }

        if (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException('Notification is missing toDatabase / toArray method.');
    }
}
