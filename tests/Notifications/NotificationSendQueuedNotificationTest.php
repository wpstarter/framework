<?php

namespace WpStarter\Tests\Notifications;

use WpStarter\Contracts\Database\ModelIdentifier;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Notifications\AnonymousNotifiable;
use WpStarter\Notifications\ChannelManager;
use WpStarter\Notifications\Notifiable;
use WpStarter\Notifications\SendQueuedNotifications;
use WpStarter\Support\Collection;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class NotificationSendQueuedNotificationTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testNotificationsCanBeSent()
    {
        $job = new SendQueuedNotifications('notifiables', 'notification');
        $manager = m::mock(ChannelManager::class);
        $manager->shouldReceive('sendNow')->once()->withArgs(function ($notifiables, $notification, $channels) {
            return $notifiables instanceof Collection && $notifiables->toArray() === ['notifiables']
                && $notification === 'notification'
                && $channels === null;
        });
        $job->handle($manager);
    }

    public function testSerializationOfNotifiableModel()
    {
        $identifier = new ModelIdentifier(NotifiableUser::class, [null], [], null);
        $serializedIdentifier = serialize($identifier);

        $job = new SendQueuedNotifications(new NotifiableUser, 'notification');
        $serialized = serialize($job);

        $this->assertStringContainsString($serializedIdentifier, $serialized);
    }

    public function testSerializationOfNormalNotifiable()
    {
        $notifiable = new AnonymousNotifiable;
        $serializedNotifiable = serialize($notifiable);

        $job = new SendQueuedNotifications($notifiable, 'notification');
        $serialized = serialize($job);

        $this->assertStringContainsString($serializedNotifiable, $serialized);
    }
}

class NotifiableUser extends Model
{
    use Notifiable;

    public $table = 'users';
    public $timestamps = false;
}
