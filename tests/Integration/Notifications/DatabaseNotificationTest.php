<?php

namespace WpStarter\Tests\Integration\Notifications;

use WpStarter\Database\Eloquent\Casts\AsStringable;
use WpStarter\Database\Eloquent\Concerns\HasUuids;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Foundation\Testing\RefreshDatabase;
use WpStarter\Notifications\Notifiable;
use WpStarter\Support\Facades\Notification;
use WpStarter\Support\Facades\Schema;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;

#[WithMigration('laravel', 'notifications')]
class DatabaseNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[DefineDatabase('defineDatabaseAndConvertUserIdToUuid')]
    public function testAssertSentToWhenNotifiableHasStringableKey()
    {
        Notification::fake();

        $user = UuidUserFactoryStub::new()->create();

        $user->notify(new NotificationStub);

        Notification::assertSentTo($user, NotificationStub::class, function ($notification, $channels, $notifiable) use ($user) {
            return $notifiable === $user;
        });
    }

    /**
     * Define database and convert User's ID to UUID.
     *
     * @param  \WpStarter\Foundation\Application  $app
     * @return void
     */
    protected function defineDatabaseAndConvertUserIdToUuid($app): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->change();
        });
    }
}

class UuidUserFactoryStub extends \Orchestra\Testbench\Factories\UserFactory
{
    protected $model = UuidUserStub::class;
}

class UuidUserStub extends \WpStarter\Foundation\Auth\User
{
    use HasUuids, Notifiable;

    protected $table = 'users';

    #[\Override]
    public function casts()
    {
        return array_merge(parent::casts(), ['id' => AsStringable::class]);
    }
}

class NotificationStub extends \WpStarter\Notifications\Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }
}
