<?php

namespace WpStarter\Support\Facades;

/**
 * @method static \WpStarter\Http\RedirectResponse action(string $action, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse away(string $path, int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse back(int $status = 302, array $headers = [], $fallback = false)
 * @method static \WpStarter\Http\RedirectResponse guest(string $path, int $status = 302, array $headers = [], bool $secure = null)
 * @method static \WpStarter\Http\RedirectResponse home(int $status = 302)
 * @method static \WpStarter\Http\RedirectResponse intended(string $default = '/', int $status = 302, array $headers = [], bool $secure = null)
 * @method static \WpStarter\Http\RedirectResponse refresh(int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse route(string $route, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse secure(string $path, int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse signedRoute(string $name, mixed $parameters = [], \DateTimeInterface|\DateInterval|int $expiration = null, int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse temporarySignedRoute(string $name, \DateTimeInterface|\DateInterval|int $expiration, mixed $parameters = [], int $status = 302, array $headers = [])
 * @method static \WpStarter\Http\RedirectResponse to(string $path, int $status = 302, array $headers = [], bool $secure = null)
 * @method static \WpStarter\Routing\UrlGenerator getUrlGenerator()
 * @method static void setSession(\WpStarter\Session\Store $session)
 * @method static void setIntendedUrl(string $url)
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
