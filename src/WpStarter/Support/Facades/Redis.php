<?php

namespace WpStarter\Support\Facades;

/**
 * @method static \WpStarter\Redis\Connections\Connection connection(string $name = null)
 * @method static \WpStarter\Redis\Limiters\ConcurrencyLimiterBuilder funnel(string $name)
 * @method static \WpStarter\Redis\Limiters\DurationLimiterBuilder throttle(string $name)
 *
 * @see \WpStarter\Redis\RedisManager
 * @see \WpStarter\Contracts\Redis\Factory
 */
class Redis extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'redis';
    }
}
