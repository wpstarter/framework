<?php

namespace WpStarter\Support\Facades;

use WpStarter\Concurrency\ConcurrencyManager;

/**
 * @method static mixed driver(string|null $name = null)
 * @method static \WpStarter\Concurrency\ProcessDriver createProcessDriver()
 * @method static \WpStarter\Concurrency\ForkDriver createForkDriver()
 * @method static \WpStarter\Concurrency\SyncDriver createSyncDriver()
 * @method static string getDefaultInstance()
 * @method static void setDefaultInstance(string $name)
 * @method static array getInstanceConfig(string $name)
 * @method static mixed instance(string|null $name = null)
 * @method static \WpStarter\Concurrency\ConcurrencyManager forgetInstance(array|string|null $name = null)
 * @method static void purge(string|null $name = null)
 * @method static \WpStarter\Concurrency\ConcurrencyManager extend(string $name, \Closure $callback)
 * @method static \WpStarter\Concurrency\ConcurrencyManager setApplication(\WpStarter\Contracts\Foundation\Application $app)
 * @method static array run(\Closure|array $tasks)
 * @method static \WpStarter\Support\Defer\DeferredCallback defer(\Closure|array $tasks)
 *
 * @see \WpStarter\Concurrency\ConcurrencyManager
 */
class Concurrency extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConcurrencyManager::class;
    }
}
