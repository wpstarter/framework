<?php

namespace WpStarter\Support\Facades;

use Laravel\Ui\UiServiceProvider;
use RuntimeException;

/**
 * @method static \WpStarter\Auth\AuthManager extend(string $driver, \Closure $callback)
 * @method static \WpStarter\Auth\AuthManager provider(string $name, \Closure $callback)
 * @method static \WpStarter\Wordpress\User|null user()
 * @method static \WpStarter\Contracts\Auth\Guard|\WpStarter\Contracts\Auth\Guard guard(string|null $name = null)
 * @method static \WpStarter\Contracts\Auth\UserProvider|null createUserProvider(string $provider = null)
 * @method static int|string|null id()
 * @method static void logout()
 * @method static void setUser(\WpStarter\Wordpress\User $user)
 *
 * @see \WpStarter\Auth\AuthManager
 * @see \WpStarter\Contracts\Auth\Factory
 * @see \WpStarter\Contracts\Auth\Guard
 */
class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth';
    }

    /**
     * Register the typical authentication routes for an application.
     *
     * @param  array  $options
     * @return void
     *
     * @throws \RuntimeException
     */
    public static function routes(array $options = [])
    {
        if (! static::$app->providerIsLoaded(UiServiceProvider::class)) {
            throw new RuntimeException('In order to use the Auth::routes() method, please install the laravel/ui package.');
        }

        static::$app->make('router')->auth($options);
    }
}
