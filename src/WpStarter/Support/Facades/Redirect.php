<?php

namespace WpStarter\Support\Facades;

/**
 * @method static \WpStarter\Http\RedirectResponse back(int $status = 302, array $headers = [], mixed $fallback = false)
 * @method static \WpStarter\Http\RedirectResponse refresh(int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse guest(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \WpStarter\Http\RedirectResponse intended(mixed $default = '/', int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \WpStarter\Http\RedirectResponse to(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \WpStarter\Http\RedirectResponse away(string $path, int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse secure(string $path, int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse route(\BackedEnum|string $route, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse signedRoute(\BackedEnum|string $route, mixed $parameters = [], \DateTimeInterface|\DateInterval|int|null $expiration = null, int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse temporarySignedRoute(\BackedEnum|string $route, \DateTimeInterface|\DateInterval|int|null $expiration, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse action(string|array $action, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \WpStarter\Routing\UrlGenerator getUrlGenerator()
 * @method static void setSession(\WpStarter\Session\Store $session)
 * @method static string|null getIntendedUrl()
 * @method static \WpStarter\Routing\Redirector setIntendedUrl(string $url)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \WpStarter\Routing\Redirector
 */
class Redirect extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'redirect';
    }
}
