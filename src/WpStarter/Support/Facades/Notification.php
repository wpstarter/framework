<?php

namespace WpStarter\Support\Facades;

use WpStarter\Notifications\AnonymousNotifiable;
use WpStarter\Notifications\ChannelManager;
use WpStarter\Support\Testing\Fakes\NotificationFake;

/**
 * @method static \WpStarter\Notifications\ChannelManager locale(string|null $locale)
 * @method static \WpStarter\Support\Collection sent(mixed $notifiable, string $notification, callable $callback = null)
 * @method static bool hasSent(mixed $notifiable, string $notification)
 * @method static mixed channel(string|null $name = null)
 * @method static void assertNotSentTo(mixed $notifiable, string|\Closure $notification, callable $callback = null)
 * @method static void assertNothingSent()
 * @method static void assertSentOnDemand(string|\Closure $notification, callable $callback = null)
 * @method static void assertSentTo(mixed $notifiable, string|\Closure $notification, callable $callback = null)
 * @method static void assertSentOnDemandTimes(string $notification, int $times = 1)
 * @method static void assertSentToTimes(mixed $notifiable, string $notification, int $times = 1)
 * @method static void assertTimesSent(int $expectedCount, string $notification)
 * @method static void send(\WpStarter\Support\Collection|array|mixed $notifiables, $notification)
 * @method static void sendNow(\WpStarter\Support\Collection|array|mixed $notifiables, $notification)
 *
 * @see \WpStarter\Notifications\ChannelManager
 */
class Notification extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return \WpStarter\Support\Testing\Fakes\NotificationFake
     */
    public static function fake()
    {
        static::swap($fake = new NotificationFake);

        return $fake;
    }

    /**
     * Begin sending a notification to an anonymous notifiable.
     *
     * @param  string  $channel
     * @param  mixed  $route
     * @return \WpStarter\Notifications\AnonymousNotifiable
     */
    public static function route($channel, $route)
    {
        return (new AnonymousNotifiable)->route($channel, $route);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ChannelManager::class;
    }
}
