<?php

namespace WpStarter\Auth\Listeners;

use WpStarter\Auth\Events\Registered;
use WpStarter\Contracts\Auth\MustVerifyEmail;

class SendEmailVerificationNotification
{
    /**
     * Handle the event.
     *
     * @param  \WpStarter\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            $event->user->sendEmailVerificationNotification();
        }
    }
}
