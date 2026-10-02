<?php

namespace WpStarter\Tests\Integration\Mail;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Foundation\Testing\LazilyRefreshDatabase;
use WpStarter\Notifications\Events\NotificationSent;
use WpStarter\Notifications\Messages\MailMessage;
use WpStarter\Notifications\Notifiable;
use WpStarter\Notifications\Notification;
use WpStarter\Support\Facades\Event;
use WpStarter\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

class SentMessageMailTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function afterRefreshingDatabase()
    {
        Schema::create('sent_message_users', function (Blueprint $table) {
            $table->increments('id');
        });
    }

    protected function beforeRefreshingDatabase()
    {
        Schema::dropIfExists('sent_message_users');
    }

    public function testDispatchesNotificationSent()
    {
        $notificationWasSent = false;

        $user = SentMessageUser::create();

        Event::listen(
            NotificationSent::class,
            function (NotificationSent $notification) use (&$notificationWasSent, $user) {
                $notificationWasSent = true;
                /**
                 * Confirm that NotificationSent can be serialized/unserialized as
                 * will happen if the listener implements ShouldQueue.
                 */
                /** @var NotificationSent $afterSerialization */
                $afterSerialization = unserialize(serialize($notification));

                $this->assertTrue($user->is($afterSerialization->notifiable));

                $this->assertEqualsCanonicalizing($notification->notification, $afterSerialization->notification);
            });

        $user->notify(new SentMessageMailNotification());

        $this->assertTrue($notificationWasSent);
    }
}

class SentMessageUser extends Model
{
    use Notifiable;

    public $timestamps = false;
}

class SentMessageMailNotification extends Notification
{
    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Example notification with attachment.')
            ->attach(__DIR__.'/Fixtures/blank_document.pdf', [
                'as' => 'blank_document.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
