<?php

namespace WpStarter\Tests\Auth;

use WpStarter\Auth\Events\Registered;
use WpStarter\Auth\Listeners\SendEmailVerificationNotification;
use WpStarter\Contracts\Auth\MustVerifyEmail;
use WpStarter\Foundation\Auth\User;
use PHPUnit\Framework\TestCase;

class AuthListenersSendEmailVerificationNotificationHandleFunctionTest extends TestCase
{
    /**
     * @return void
     */
    public function testWillExecuted()
    {
        $user = $this->getMockBuilder(MustVerifyEmail::class)->getMock();
        $user->method('hasVerifiedEmail')->willReturn(false);
        $user->expects($this->once())->method('sendEmailVerificationNotification');

        $listener = new SendEmailVerificationNotification;

        $listener->handle(new Registered($user));
    }

    /**
     * @return void
     */
    public function testUserIsNotInstanceOfMustVerifyEmail()
    {
        $user = $this->getMockBuilder(User::class)->getMock();
        $user->expects($this->never())->method('sendEmailVerificationNotification');

        $listener = new SendEmailVerificationNotification;

        $listener->handle(new Registered($user));
    }

    /**
     * @return void
     */
    public function testHasVerifiedEmailAsTrue()
    {
        $user = $this->getMockBuilder(MustVerifyEmail::class)->getMock();
        $user->method('hasVerifiedEmail')->willReturn(true);
        $user->expects($this->never())->method('sendEmailVerificationNotification');

        $listener = new SendEmailVerificationNotification;

        $listener->handle(new Registered($user));
    }
}
