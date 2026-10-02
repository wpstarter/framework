<?php

namespace WpStarter\Support\Facades;

use WpStarter\Foundation\MaintenanceModeManager;

/**
 * @method static string getDefaultDriver()
 * @method static mixed driver(string|null $driver = null)
 * @method static \WpStarter\Foundation\MaintenanceModeManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \WpStarter\Contracts\Container\Container getContainer()
 * @method static \WpStarter\Foundation\MaintenanceModeManager setContainer(\WpStarter\Contracts\Container\Container $container)
 * @method static \WpStarter\Foundation\MaintenanceModeManager forgetDrivers()
 *
 * @see \WpStarter\Foundation\MaintenanceModeManager
 */
class MaintenanceMode extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MaintenanceModeManager::class;
    }
}
