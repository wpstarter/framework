<?php

namespace WpStarter\Tests\Integration\Auth;

use WpStarter\Auth\Notifications\ResetPassword;
use WpStarter\Notifications\Messages\MailMessage;
use WpStarter\Support\Facades\Notification;
use WpStarter\Support\Facades\Password;
use WpStarter\Tests\Integration\Auth\Fixtures\AuthenticationTestUser;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\TestCase;

class ForgotPasswordTest extends TestCase
{
    protected function tearDown(): void
    {
        ResetPassword::$createUrlCallback = null;
        ResetPassword::$toMailCallback = null;

        parent::tearDown();
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('auth.providers.users.model', AuthenticationTestUser::class);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
    }

    protected function defineRoutes($router)
    {
        $router->get('password/reset/{token}', function ($token) {
            return 'Reset password!';
        })->name('password.reset');

        $router->get('custom/password/reset/{token}', function ($token) {
            return 'Custom reset password!';
        })->name('custom.password.reset');
    }

    /** @test */
    public function it_can_send_forgot_password_email()
    {
        Notification::fake();

        UserFactory::new()->create();

        $user = AuthenticationTestUser::first();

        Password::broker()->sendResetLink([
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            function (ResetPassword $notification, $channels) use ($user) {
                $message = $notification->toMail($user);

                return ! is_null($notification->token)
                    && $message->actionUrl === ws_route('password.reset', ['token' => $notification->token, 'email' => $user->email]);
            }
        );
    }

    /** @test */
    public function it_can_send_forgot_password_email_via_create_url_using()
    {
        Notification::fake();

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return ws_route('custom.password.reset', $token);
        });

        UserFactory::new()->create();

        $user = AuthenticationTestUser::first();

        Password::broker()->sendResetLink([
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            function (ResetPassword $notification, $channels) use ($user) {
                $message = $notification->toMail($user);

                return ! is_null($notification->token)
                    && $message->actionUrl === ws_route('custom.password.reset', ['token' => $notification->token]);
            }
        );
    }

    /** @test */
    public function it_can_send_forgot_password_email_via_to_mail_using()
    {
        Notification::fake();

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            return (new MailMessage)
                ->subject(ws___('Reset Password Notification'))
                ->line(ws___('You are receiving this email because we received a password reset request for your account.'))
                ->action(ws___('Reset Password'), ws_route('custom.password.reset', $token))
                ->line(ws___('If you did not request a password reset, no further action is required.'));
        });

        UserFactory::new()->create();

        $user = AuthenticationTestUser::first();

        Password::broker()->sendResetLink([
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            function (ResetPassword $notification, $channels) use ($user) {
                $message = $notification->toMail($user);

                return ! is_null($notification->token)
                    && $message->actionUrl === ws_route('custom.password.reset', ['token' => $notification->token]);
            }
        );
    }
}
