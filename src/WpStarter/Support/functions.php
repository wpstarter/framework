<?php

namespace WpStarter\Support;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use WpStarter\Support\Defer\DeferredCallback;
use WpStarter\Support\Defer\DeferredCallbackCollection;
use WpStarter\Support\Facades\Date;
use Symfony\Component\Process\PhpExecutableFinder;

if (! function_exists('WpStarter\Support\defer')) {
    /**
     * Defer execution of the given callback.
     *
     * @param  callable|null  $callback
     * @param  string|null  $name
     * @param  bool  $always
     * @return ($callback is null ? \WpStarter\Support\Defer\DeferredCallbackCollection : \WpStarter\Support\Defer\DeferredCallback)
     */
    function defer(?callable $callback = null, ?string $name = null, bool $always = false): DeferredCallback|DeferredCallbackCollection
    {
        if ($callback === null) {
            return ws_app(DeferredCallbackCollection::class);
        }

        return ws_tap(
            new DeferredCallback($callback, $name, $always),
            fn ($deferred) => ws_app(DeferredCallbackCollection::class)[] = $deferred
        );
    }
}

if (! function_exists('WpStarter\Support\php_binary')) {
    /**
     * Determine the PHP Binary.
     */
    function php_binary(): string
    {
        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }
}

if (! function_exists('WpStarter\Support\artisan_binary')) {
    /**
     * Determine the proper Artisan executable.
     */
    function artisan_binary(): string
    {
        return defined('ARTISAN_BINARY') ? ARTISAN_BINARY : 'artisan';
    }
}

// Time functions...

if (! function_exists('WpStarter\Support\now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param  \DateTimeZone|\UnitEnum|string|null  $tz
     * @return \WpStarter\Support\Carbon
     */
    function now($tz = null): CarbonInterface
    {
        return Date::now(enum_value($tz));
    }
}

if (! function_exists('WpStarter\Support\microseconds')) {
    /**
     * Get the current date / time plus the given number of microseconds.
     */
    function microseconds(int|float $microseconds): CarbonInterval
    {
        return CarbonInterval::microseconds($microseconds);
    }
}

if (! function_exists('WpStarter\Support\milliseconds')) {
    /**
     * Get the current date / time plus the given number of milliseconds.
     */
    function milliseconds(int|float $milliseconds): CarbonInterval
    {
        return CarbonInterval::milliseconds($milliseconds);
    }
}

if (! function_exists('WpStarter\Support\seconds')) {
    /**
     * Get the current date / time plus the given number of seconds.
     */
    function seconds(int|float $seconds): CarbonInterval
    {
        return CarbonInterval::seconds($seconds);
    }
}

if (! function_exists('WpStarter\Support\minutes')) {
    /**
     * Get the current date / time plus the given number of minutes.
     */
    function minutes(int|float $minutes): CarbonInterval
    {
        return CarbonInterval::minutes($minutes);
    }
}

if (! function_exists('WpStarter\Support\hours')) {
    /**
     * Get the current date / time plus the given number of hours.
     */
    function hours(int|float $hours): CarbonInterval
    {
        return CarbonInterval::hours($hours);
    }
}

if (! function_exists('WpStarter\Support\days')) {
    /**
     * Get the current date / time plus the given number of days.
     */
    function days(int|float $days): CarbonInterval
    {
        return CarbonInterval::days($days);
    }
}

if (! function_exists('WpStarter\Support\weeks')) {
    /**
     * Get the current date / time plus the given number of weeks.
     */
    function weeks(int $weeks): CarbonInterval
    {
        return CarbonInterval::weeks($weeks);
    }
}

if (! function_exists('WpStarter\Support\months')) {
    /**
     * Get the current date / time plus the given number of months.
     */
    function months(int $months): CarbonInterval
    {
        return CarbonInterval::months($months);
    }
}

if (! function_exists('WpStarter\Support\years')) {
    /**
     * Get the current date / time plus the given number of years.
     */
    function years(int $years): CarbonInterval
    {
        return CarbonInterval::years($years);
    }
}
