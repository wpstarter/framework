<?php

namespace WpStarter\Support\Facades;

/**
 * @method static string full()
 * @method static string current()
 * @method static string previous(mixed $fallback = false)
 * @method static string previousPath(mixed $fallback = false)
 * @method static string to(string $path, mixed $extra = [], bool|null $secure = null)
 * @method static string query(string $path, array $query = [], mixed $extra = [], bool|null $secure = null)
 * @method static string secure(string $path, array $parameters = [])
 * @method static string asset(string $path, bool|null $secure = null)
 * @method static string secureAsset(string $path)
 * @method static string assetFrom(string $root, string $path, bool|null $secure = null)
 * @method static string formatScheme(bool|null $secure = null)
 * @method static string signedRoute(\BackedEnum|string $name, mixed $parameters = [], \DateTimeInterface|\DateInterval|int|null $expiration = null, bool $absolute = true)
 * @method static string temporarySignedRoute(\BackedEnum|string $name, \DateTimeInterface|\DateInterval|int $expiration, array $parameters = [], bool $absolute = true)
 * @method static bool hasValidSignature(\WpStarter\Http\Request $request, bool $absolute = true, \Closure|array $ignoreQuery = [])
 * @method static bool hasValidRelativeSignature(\WpStarter\Http\Request $request, \Closure|array $ignoreQuery = [])
 * @method static bool hasCorrectSignature(\WpStarter\Http\Request $request, bool $absolute = true, \Closure|array $ignoreQuery = [])
 * @method static bool signatureHasNotExpired(\WpStarter\Http\Request $request)
 * @method static string route(\BackedEnum|string $name, mixed $parameters = [], bool $absolute = true)
 * @method static string toRoute(\WpStarter\Routing\Route $route, mixed $parameters, bool $absolute)
 * @method static string action(string|array $action, mixed $parameters = [], bool $absolute = true)
 * @method static array formatParameters(mixed $parameters)
 * @method static string formatRoot(string $scheme, string|null $root = null)
 * @method static string format(string $root, string $path, \WpStarter\Routing\Route|null $route = null)
 * @method static bool isValidUrl(string $path)
 * @method static void defaults(array $defaults)
 * @method static array getDefaultParameters()
 * @method static void forceScheme(string|null $scheme)
 * @method static void forceHttps(bool $force = true)
 * @method static void useOrigin(string|null $root)
 * @method static void useAssetOrigin(string|null $root)
 * @method static \WpStarter\Routing\UrlGenerator formatHostUsing(\Closure $callback)
 * @method static \WpStarter\Routing\UrlGenerator formatPathUsing(\Closure $callback)
 * @method static \Closure pathFormatter()
 * @method static \WpStarter\Http\Request getRequest()
 * @method static void setRequest(\WpStarter\Http\Request $request)
 * @method static \WpStarter\Routing\UrlGenerator setRoutes(\WpStarter\Routing\RouteCollectionInterface $routes)
 * @method static \WpStarter\Routing\UrlGenerator setSessionResolver(callable $sessionResolver)
 * @method static \WpStarter\Routing\UrlGenerator setKeyResolver(callable $keyResolver)
 * @method static \WpStarter\Routing\UrlGenerator withKeyResolver(callable $keyResolver)
 * @method static \WpStarter\Routing\UrlGenerator resolveMissingNamedRoutesUsing(callable $missingNamedRouteResolver)
 * @method static string getRootControllerNamespace()
 * @method static \WpStarter\Routing\UrlGenerator setRootControllerNamespace(string $rootNamespace)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \WpStarter\Routing\UrlGenerator
 */
class URL extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'url';
    }
}
