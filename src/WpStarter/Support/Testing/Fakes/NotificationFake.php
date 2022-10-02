<?php

namespace WpStarter\Support\Testing\Fakes;

use Closure;
use Exception;
use WpStarter\Contracts\Notifications\Dispatcher as NotificationDispatcher;
use WpStarter\Contracts\Notifications\Factory as NotificationFactory;
use WpStarter\Contracts\Translation\HasLocalePreference;
use WpStarter\Notifications\AnonymousNotifiable;
use WpStarter\Support\Collection;
use WpStarter\Support\Str;
use WpStarter\Support\Traits\Macroable;
use WpStarter\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;

class NotificationFake implements NotificationDispatcher, NotificationFactory
{
    use Macroable, ReflectsClosures;

    /**
     * All of the notifications that have been sent.
     *
     * @var array
     */
    protected $notifications = [];

    /**
     * Locale used when sending notifications.
     *
     * @var string|null
     */
    public $locale;

    /**
     * Assert if a notification was sent on-demand based on a truth-test callback.
     *
     * @param  string|\Closure  $notification
     * @param  callable|null  $callback
     * @return void
     *
     * @throws \Exception
     */
    public function assertSentOnDemand($notification, $callback = null)
    {
        $this->assertSentTo(new AnonymousNotifiable, $notification, $callback);
    }

    /**
     * Assert if a notification was sent based on a truth-test callback.
     *
     * @param  mixed  $notifiable
     * @param  string|\Closure  $notification
     * @param  callable|null  $callback
     * @return void
     *
     * @throws \Exception
     */
    public function assertSentTo($notifiable, $notification, $callback = null)
    {
        if (is_array($notifiable) || $notifiable instanceof Collection) {
            if (count($notifiable) === 0) {
                throw new Exception('No notifiable given.');
            }

            foreach ($notifiable as $singleNotifiable) {
                $this->assertSentTo($singleNotifiable, $notification, $callback);
            }

            return;
        }

        if ($notification instanceof Closure) {
            [$notification, $callback] = [$this->firstClosureParameterType($notification), $notification];
        }

        if (is_numeric($callback)) {
            return $this->assertSentToTimes($notifiable, $notification, $callback);
        }

        PHPUnit::assertTrue(
            $this->sent($notifiable, $notification, $callback)->count() > 0,
            "The expected [{$notification}] notification was not sent."
        );
    }

    /**
     * Assert if a notification was sent on-demand a number of times.
     *
     * @param  string  $notification
     * @param  int  $times
     * @return void
     */
    public function assertSentOnDemandTimes($notification, $times = 1)
    {
        return $this->assertSentToTimes(new AnonymousNotifiable, $notification, $times);
    }

    /**
     * Assert if a notification was sent a number of times.
     *
     * @param  mixed  $notifiable
     * @param  string  $notification
     * @param  int  $times
     * @return void
     */
    public function assertSentToTimes($notifiable, $notification, $times = 1)
    {
        $count = $this->sent($notifiable, $notification)->count();

        PHPUnit::assertSame(
            $times, $count,
            "Expected [{$notification}] to be sent {$times} times, but was sent {$count} times."
        );
    }

    /**
     * Determine if a notification was sent based on a truth-test callback.
     *
     * @param  mixed  $notifiable
     * @param  string|\Closure  $notification
     * @param  callable|null  $callback
     * @return void
     *
     * @throws \Exception
     */
    public function assertNotSentTo($notifiable, $notification, $callback = null)
    {
        if (is_array($notifiable) || $notifiable instanceof Collection) {
            if (count($notifiable) === 0) {
                throw new Exception('No notifiable given.');
            }

            foreach ($notifiable as $singleNotifiable) {
                $this->assertNotSentTo($singleNotifiable, $notification, $callback);
            }

            return;
        }

        if ($notification instanceof Closure) {
            [$notification, $callback] = [$this->firstClosureParameterType($notification), $notification];
        }

        PHPUnit::assertCount(
            0, $this->sent($notifiable, $notification, $callback),
            "The unexpected [{$notification}] notification was sent."
        );
    }

    /**
     * Assert that no notifications were sent.
     *
     * @return void
     */
    public function assertNothingSent()
    {
        PHPUnit::assertEmpty($this->notifications, 'Notifications were sent unexpectedly.');
    }

    /**
     * Assert the total amount of times a notification was sent.
     *
     * @param  string  $notification
     * @param  int  $expectedCount
     * @return void
     */
    public function assertSentTimes($notification, $expectedCount)
    {
        $actualCount = ws_collect($this->notifications)
            ->flatten(1)
            ->reduce(function ($count, $sent) use ($notification) {
                return $count + count($sent[$notification] ?? []);
            }, 0);

        PHPUnit::assertSame(
            $expectedCount, $actualCount,
            "Expected [{$notification}] to be sent {$expectedCount} times, but was sent {$actualCount} times."
        );
    }

    /**
     * Assert the total amount of times a notification was sent.
     *
     * @param  int  $expectedCount
     * @param  string  $notification
     * @return void
     *
     * @deprecated Use the assertSentTimes method instead
     */
    public function assertTimesSent($expectedCount, $notification)
    {
        $this->assertSentTimes($notification, $expectedCount);
    }

    /**
     * Get all of the notifications matching a truth-test callback.
     *
     * @param  mixed  $notifiable
     * @param  string  $notification
     * @param  callable|null  $callback
     * @return \WpStarter\Support\Collection
     */
    public function sent($notifiable, $notification, $callback = null)
    {
        if (! $this->hasSent($notifiable, $notification)) {
            return ws_collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        $notifications = ws_collect($this->notificationsFor($notifiable, $notification));

        return $notifications->filter(function ($arguments) use ($callback) {
            return $callback(...array_values($arguments));
        })->pluck('notification');
    }

    /**
     * Determine if there are more notifications left to inspect.
     *
     * @param  mixed  $notifiable
     * @param  string  $notification
     * @return bool
     */
    public function hasSent($notifiable, $notification)
    {
        return ! empty($this->notificationsFor($notifiable, $notification));
    }

    /**
     * Get all of the notifications for a notifiable entity by type.
     *
     * @param  mixed  $notifiable
     * @param  string  $notification
     * @return array
     */
    protected function notificationsFor($notifiable, $notification)
    {
        return $this->notifications[get_class($notifiable)][$notifiable->getKey()][$notification] ?? [];
    }

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \WpStarter\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $this->sendNow($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \WpStarter\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @param  array|null  $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, array $channels = null)
    {
        if (! $notifiables instanceof Collection && ! is_array($notifiables)) {
            $notifiables = [$notifiables];
        }

        foreach ($notifiables as $notifiable) {
            if (! $notification->id) {
                $notification->id = Str::uuid()->toString();
            }

            $notifiableChannels = $channels ?: $notification->via($notifiable);

            if (method_exists($notification, 'shouldSend')) {
                $notifiableChannels = array_filter(
                    $notifiableChannels,
                    function ($channel) use ($notification, $notifiable) {
                        return $notification->shouldSend($notifiable, $channel) !== false;
                    }
                );

                if (empty($notifiableChannels)) {
                    continue;
                }
            }

            $this->notifications[get_class($notifiable)][$notifiable->getKey()][get_class($notification)][] = [
                'notification' => $notification,
                'channels' => $notifiableChannels,
                'notifiable' => $notifiable,
                'locale' => $notification->locale ?? $this->locale ?? ws_value(function () use ($notifiable) {
                    if ($notifiable instanceof HasLocalePreference) {
                        return $notifiable->preferredLocale();
                    }
                }),
            ];
        }
    }

    /**
     * Get a channel instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel($name = null)
    {
        //
    }

    /**
     * Set the locale of notifications.
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
