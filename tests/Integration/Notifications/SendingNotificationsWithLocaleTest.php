<?php

namespace WpStarter\Tests\Integration\Notifications;

use WpStarter\Contracts\Translation\HasLocalePreference;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Foundation\Events\LocaleUpdated;
use WpStarter\Mail\Mailable;
use WpStarter\Notifications\Channels\MailChannel;
use WpStarter\Notifications\Messages\MailMessage;
use WpStarter\Notifications\Notifiable;
use WpStarter\Notifications\Notification;
use WpStarter\Support\Carbon;
use WpStarter\Support\Facades\Event;
use WpStarter\Support\Facades\Notification as NotificationFacade;
use WpStarter\Support\Facades\Schema;
use WpStarter\Support\Facades\View;
use WpStarter\Testing\Assert;
use Orchestra\Testbench\TestCase;

class SendingNotificationsWithLocaleTest extends TestCase
{
    public $mailer;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mail.driver', 'array');

        $app['config']->set('app.locale', 'en');

        View::addLocation(__DIR__.'/Fixtures');

        app('translator')->setLoaded([
            '*' => [
                '*' => [
                    'en' => ['hi' => 'hello'],
                    'es' => ['hi' => 'hola'],
                    'fr' => ['hi' => 'bonjour'],
                ],
            ],
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('name')->nullable();
        });
    }

    public function testMailIsSentWithDefaultLocale()
    {
        $user = NotifiableLocalizedUser::forceCreate([
            'email' => 'taylor@laravel.com',
            'name' => 'Taylor Otwell',
        ]);

        NotificationFacade::send($user, new GreetingMailNotification);

        $this->assertStringContainsString('hello',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );
    }

    public function testMailIsSentWithFacadeSelectedLocale()
    {
        $user = NotifiableLocalizedUser::forceCreate([
            'email' => 'taylor@laravel.com',
            'name' => 'Taylor Otwell',
        ]);

        NotificationFacade::locale('fr')->send($user, new GreetingMailNotification);

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );
    }

    public function testMailIsSentWithNotificationSelectedLocale()
    {
        $users = [
            NotifiableLocalizedUser::forceCreate([
                'email' => 'taylor@laravel.com',
                'name' => 'Taylor Otwell',
            ]),
            NotifiableLocalizedUser::forceCreate([
                'email' => 'mohamed@laravel.com',
                'name' => 'Mohamed Said',
            ]),
        ];

        NotificationFacade::send($users, (new GreetingMailNotification)->locale('fr'));

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[1]->getBody()
        );
    }

    public function testMailableIsSentWithSelectedLocale()
    {
        $user = NotifiableLocalizedUser::forceCreate([
            'email' => 'taylor@laravel.com',
            'name' => 'Taylor Otwell',
        ]);

        NotificationFacade::locale('fr')->send($user, new GreetingMailNotificationWithMailable);

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );
    }

    public function testMailIsSentWithLocaleUpdatedListenersCalled()
    {
        Carbon::setTestNow('2018-07-25');

        Event::listen(LocaleUpdated::class, function ($event) {
            Carbon::setLocale($event->locale);
        });

        $user = NotifiableLocalizedUser::forceCreate([
            'email' => 'taylor@laravel.com',
            'name' => 'Taylor Otwell',
        ]);

        $user->notify((new GreetingMailNotification)->locale('fr'));

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );

        Assert::assertMatchesRegularExpression('/dans (1|un) jour/',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );

        $this->assertTrue($this->app->isLocale('en'));

        $this->assertSame('en', Carbon::getLocale());
    }

    public function testLocaleIsSentWithNotifiablePreferredLocale()
    {
        $recipient = new NotifiableEmailLocalePreferredUser([
            'email' => 'test@mail.com',
            'email_locale' => 'fr',
        ]);

        $recipient->notify(new GreetingMailNotification);

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );
    }

    public function testLocaleIsSentWithNotifiablePreferredLocaleForMultipleRecipients()
    {
        $recipients = [
            new NotifiableEmailLocalePreferredUser([
                'email' => 'test@mail.com',
                'email_locale' => 'fr',
            ]),
            new NotifiableEmailLocalePreferredUser([
                'email' => 'test.2@mail.com',
                'email_locale' => 'es',
            ]),
            NotifiableLocalizedUser::forceCreate([
                'email' => 'test.3@mail.com',
            ]),
        ];

        NotificationFacade::send(
            $recipients, new GreetingMailNotification
        );

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );
        $this->assertStringContainsString('hola',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[1]->getBody()
        );
        $this->assertStringContainsString('hello',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[2]->getBody()
        );
    }

    public function testLocaleIsSentWithNotificationSelectedLocaleOverridingNotifiablePreferredLocale()
    {
        $recipient = new NotifiableEmailLocalePreferredUser([
            'email' => 'test@mail.com',
            'email_locale' => 'es',
        ]);

        $recipient->notify(
            (new GreetingMailNotification)->locale('fr')
        );

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );
    }

    public function testLocaleIsSentWithFacadeSelectedLocaleOverridingNotifiablePreferredLocale()
    {
        $recipient = new NotifiableEmailLocalePreferredUser([
            'email' => 'test@mail.com',
            'email_locale' => 'es',
        ]);

        NotificationFacade::locale('fr')->send(
            $recipient, new GreetingMailNotification
        );

        $this->assertStringContainsString('bonjour',
            app('mailer')->getSwiftMailer()->getTransport()->messages()[0]->getBody()
        );
    }
}

class NotifiableLocalizedUser extends Model
{
    use Notifiable;

    public $table = 'users';
    public $timestamps = false;
}

class NotifiableEmailLocalePreferredUser extends Model implements HasLocalePreference
{
    use Notifiable;

    protected $fillable = [
        'email',
        'email_locale',
    ];

    public function preferredLocale()
    {
        return $this->email_locale;
    }
}

class GreetingMailNotification extends Notification
{
    public function via($notifiable)
    {
        return [MailChannel::class];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting(__('hi'))
            ->line(Carbon::tomorrow()->diffForHumans());
    }
}

class GreetingMailNotificationWithMailable extends Notification
{
    public function via($notifiable)
    {
        return [MailChannel::class];
    }

    public function toMail($notifiable)
    {
        return new GreetingMailable;
    }
}

class GreetingMailable extends Mailable
{
    public function build()
    {
        return $this->view('greeting');
    }
}
