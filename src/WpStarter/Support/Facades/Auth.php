<?php

namespace WpStarter\Support\Facades;

use WpStarter\Ui\UiServiceProvider;
use RuntimeException;

/**
 * @method static \WpStarter\Contracts\Auth\Guard|\WpStarter\Contracts\Auth\StatefulGuard guard(string|null $name = null)
 * @method static \WpStarter\Auth\SessionGuard createSessionDriver(string $name, array $config)
 * @method static \WpStarter\Auth\TokenGuard createTokenDriver(string $name, array $config)
 * @method static string getDefaultDriver()
 * @method static void shouldUse(string $name)
 * @method static void setDefaultDriver(string $name)
 * @method static \WpStarter\Auth\AuthManager viaRequest(string $driver, callable $callback)
 * @method static \Closure userResolver()
 * @method static \WpStarter\Auth\AuthManager resolveUsersUsing(\Closure $userResolver)
 * @method static \WpStarter\Auth\AuthManager extend(string $driver, \Closure $callback)
 * @method static \WpStarter\Auth\AuthManager provider(string $name, \Closure $callback)
 * @method static bool hasResolvedGuards()
 * @method static \WpStarter\Auth\AuthManager forgetGuards()
 * @method static \WpStarter\Auth\AuthManager setApplication(\WpStarter\Contracts\Foundation\Application $app)
 * @method static \WpStarter\Contracts\Auth\UserProvider|null createUserProvider(string|null $provider = null)
 * @method static string getDefaultUserProvider()
 * @method static bool check()
 * @method static bool guest()
 * @method static \WpStarter\Contracts\Auth\Authenticatable|null user()
 * @method static int|string|null id()
 * @method static bool validate(array $credentials = [])
 * @method static bool hasUser()
 * @method static \WpStarter\Contracts\Auth\Guard setUser(\WpStarter\Contracts\Auth\Authenticatable $user)
 * @method static bool attempt(array $credentials = [], bool $remember = false)
 * @method static bool once(array $credentials = [])
 * @method static void login(\WpStarter\Contracts\Auth\Authenticatable $user, bool $remember = false)
 * @method static \WpStarter\Contracts\Auth\Authenticatable|false loginUsingId(mixed $id, bool $remember = false)
 * @method static \WpStarter\Contracts\Auth\Authenticatable|false onceUsingId(mixed $id)
 * @method static bool viaRemember()
 * @method static void logout()
 * @method static \Symfony\Component\HttpFoundation\Response|null basic(string $field = 'email', array $extraConditions = [])
 * @method static \Symfony\Component\HttpFoundation\Response|null onceBasic(string $field = 'email', array $extraConditions = [])
 * @method static bool attemptWhen(array $credentials = [], array|callable|null $callbacks = null, bool $remember = false)
 * @method static string hashPasswordForCookie(string $passwordHash)
 * @method static void logoutCurrentDevice()
 * @method static \WpStarter\Contracts\Auth\Authenticatable|null logoutOtherDevices(string $password)
 * @method static void attempting(mixed $callback)
 * @method static \WpStarter\Contracts\Auth\Authenticatable getLastAttempted()
 * @method static string getName()
 * @method static string getRecallerName()
 * @method static \WpStarter\Auth\SessionGuard setRememberDuration(int $minutes)
 * @method static \WpStarter\Contracts\Cookie\QueueingFactory getCookieJar()
 * @method static void setCookieJar(\WpStarter\Contracts\Cookie\QueueingFactory $cookie)
 * @method static \WpStarter\Contracts\Events\Dispatcher getDispatcher()
 * @method static void setDispatcher(\WpStarter\Contracts\Events\Dispatcher $events)
 * @method static \WpStarter\Contracts\Session\Session getSession()
 * @method static \WpStarter\Contracts\Auth\Authenticatable|null getUser()
 * @method static \Symfony\Component\HttpFoundation\Request getRequest()
 * @method static \WpStarter\Auth\SessionGuard setRequest(\Symfony\Component\HttpFoundation\Request $request)
 * @method static \WpStarter\Support\Timebox getTimebox()
 * @method static \WpStarter\Contracts\Auth\Authenticatable authenticate()
 * @method static \WpStarter\Auth\SessionGuard forgetUser()
 * @method static \WpStarter\Contracts\Auth\UserProvider getProvider()
 * @method static void setProvider(\WpStarter\Contracts\Auth\UserProvider $provider)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \WpStarter\Auth\AuthManager
 * @see \WpStarter\Auth\SessionGuard
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
