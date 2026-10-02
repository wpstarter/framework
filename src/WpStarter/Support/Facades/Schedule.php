<?php

namespace WpStarter\Support\Facades;

use WpStarter\Console\Scheduling\Schedule as ConsoleSchedule;

/**
 * @method static \WpStarter\Console\Scheduling\CallbackEvent call(string|callable $callback, array $parameters = [])
 * @method static \WpStarter\Console\Scheduling\Event command(\Symfony\Component\Console\Command\Command|string $command, array $parameters = [])
 * @method static \WpStarter\Console\Scheduling\CallbackEvent job(object|string $job, \UnitEnum|string|null $queue = null, \UnitEnum|string|null $connection = null)
 * @method static \WpStarter\Console\Scheduling\Event exec(string $command, array $parameters = [])
 * @method static void group(\Closure $events)
 * @method static string compileArrayInput(string|int $key, array $value)
 * @method static bool serverShouldRun(\WpStarter\Console\Scheduling\Event $event, \DateTimeInterface $time)
 * @method static \WpStarter\Support\Collection dueEvents(\WpStarter\Contracts\Foundation\Application $app)
 * @method static \WpStarter\Console\Scheduling\Event[] events()
 * @method static \WpStarter\Console\Scheduling\Schedule useCache(\UnitEnum|string $store)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes withoutOverlapping(int $expiresAt = 1440)
 * @method static void mergeAttributes(\WpStarter\Console\Scheduling\Event $event)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes user(string $user)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes environments(mixed $environments)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes evenInMaintenanceMode()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes onOneServer()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes runInBackground()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes when(\Closure|bool $callback)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes skip(\Closure|bool $callback)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes name(string $description)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes description(string $description)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes cron(string $expression)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes between(string $startTime, string $endTime)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes unlessBetween(string $startTime, string $endTime)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everySecond()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyTwoSeconds()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyFiveSeconds()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyTenSeconds()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyFifteenSeconds()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyTwentySeconds()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyThirtySeconds()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyMinute()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyTwoMinutes()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyThreeMinutes()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyFourMinutes()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyFiveMinutes()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyTenMinutes()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyFifteenMinutes()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyThirtyMinutes()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes hourly()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes hourlyAt(array|string|int|int[] $offset)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyOddHour(array|string|int $offset = 0)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyTwoHours(array|string|int $offset = 0)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyThreeHours(array|string|int $offset = 0)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everyFourHours(array|string|int $offset = 0)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes everySixHours(array|string|int $offset = 0)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes daily()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes at(string $time)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes dailyAt(string $time)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes twiceDaily(int $first = 1, int $second = 13)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes twiceDailyAt(int $first = 1, int $second = 13, int $offset = 0)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes weekdays()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes weekends()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes mondays()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes tuesdays()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes wednesdays()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes thursdays()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes fridays()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes saturdays()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes sundays()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes weekly()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes weeklyOn(mixed $dayOfWeek, string $time = '0:0')
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes monthly()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes monthlyOn(int $dayOfMonth = 1, string $time = '0:0')
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes twiceMonthly(int $first = 1, int $second = 16, string $time = '0:0')
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes lastDayOfMonth(string $time = '0:0')
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes daysOfMonth(array|int ...$days)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes quarterly()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes quarterlyOn(int $dayOfQuarter = 1, string $time = '0:0')
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes yearly()
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes yearlyOn(int $month = 1, int|string $dayOfMonth = 1, string $time = '0:0')
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes days(mixed $days)
 * @method static \WpStarter\Console\Scheduling\PendingEventAttributes timezone(\UnitEnum|\DateTimeZone|string $timezone)
 *
 * @see \WpStarter\Console\Scheduling\Schedule
 */
class Schedule extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConsoleSchedule::class;
    }
}
