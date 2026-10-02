<?php

use Carbon\CarbonInterface;
use WpStarter\Broadcasting\FakePendingBroadcast;
use WpStarter\Broadcasting\PendingBroadcast;
use WpStarter\Container\Container;
use WpStarter\Contracts\Auth\Access\Gate;
use WpStarter\Contracts\Auth\Factory as AuthFactory;
use WpStarter\Contracts\Auth\Guard;
use WpStarter\Contracts\Broadcasting\Factory as BroadcastFactory;
use WpStarter\Contracts\Bus\Dispatcher;
use WpStarter\Contracts\Cookie\Factory as CookieFactory;
use WpStarter\Contracts\Debug\ExceptionHandler;
use WpStarter\Contracts\Routing\ResponseFactory;
use WpStarter\Contracts\Routing\UrlGenerator;
use WpStarter\Contracts\Support\Responsable;
use WpStarter\Contracts\Translation\Translator;
use WpStarter\Contracts\Validation\Factory as ValidationFactory;
use WpStarter\Contracts\Validation\Validator as ValidatorContract;
use WpStarter\Contracts\View\Factory as ViewFactory;
use WpStarter\Contracts\View\View as ViewContract;
use WpStarter\Cookie\CookieJar;
use WpStarter\Foundation\Bus\PendingClosureDispatch;
use WpStarter\Foundation\Bus\PendingDispatch;
use WpStarter\Foundation\Mix;
use WpStarter\Http\Exceptions\HttpResponseException;
use WpStarter\Http\RedirectResponse;
use WpStarter\Http\Response as IlluminateResponse;
use WpStarter\Log\Context\Repository as ContextRepository;
use WpStarter\Log\LogManager;
use WpStarter\Queue\CallQueuedClosure;
use WpStarter\Routing\Redirector;
use WpStarter\Routing\Router;
use WpStarter\Support\Defer\DeferredCallback;
use WpStarter\Support\Defer\DeferredCallbackCollection;
use WpStarter\Support\Facades\Date;
use WpStarter\Support\Facades\Route;
use WpStarter\Support\HtmlString;
use WpStarter\Support\Uri;
use League\Uri\Contracts\UriInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

use function WpStarter\Support\enum_value;

if (! function_exists('ws_abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param  \Symfony\Component\HttpFoundation\Response|\WpStarter\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     * @return never
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \WpStarter\Http\Exceptions\HttpResponseException
     */
    function ws_abort($code, $message = '', array $headers = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        } elseif ($code instanceof Responsable) {
            throw new HttpResponseException($code->toResponse(ws_request()));
        }

        ws_app()->abort($code, $message, $headers);
    }
}

if (! function_exists('ws_abort_if')) {
    /**
     * Throw an HttpException with the given data if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Symfony\Component\HttpFoundation\Response|\WpStarter\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function ws_abort_if($boolean, $code, $message = '', array $headers = []): void
    {
        if ($boolean) {
            ws_abort($code, $message, $headers);
        }
    }
}

if (! function_exists('ws_abort_unless')) {
    /**
     * Throw an HttpException with the given data unless the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Symfony\Component\HttpFoundation\Response|\WpStarter\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function ws_abort_unless($boolean, $code, $message = '', array $headers = []): void
    {
        if (! $boolean) {
            ws_abort($code, $message, $headers);
        }
    }
}

if (! function_exists('ws_action')) {
    /**
     * Generate the URL to a controller action.
     *
     * @param  string|array  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    function ws_action($name, $parameters = [], $absolute = true): string
    {
        return ws_app('url')->action($name, $parameters, $absolute);
    }
}

if (! function_exists('ws_app')) {
    /**
     * Get the available container instance.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>|null  $abstract
     * @return ($abstract is class-string<TClass> ? TClass : ($abstract is null ? \WpStarter\Foundation\Application : mixed))
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     */
    function app_path($path = ''): string
    {
        return ws_app()->path($path);
    }
}

if (! function_exists('ws_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     */
    function ws_asset($path, $secure = null): string
    {
        return ws_app('url')->asset($path, $secure);
    }
}

if (! function_exists('ws_auth')) {
    /**
     * Get the available auth instance.
     *
     * @param  string|null  $guard
     * @return ($guard is null ? \WpStarter\Contracts\Auth\Factory : \WpStarter\Contracts\Auth\Guard)
     */
    function ws_auth($guard = null): AuthFactory|Guard
    {
        if (is_null($guard)) {
            return ws_app(AuthFactory::class);
        }

        return ws_app(AuthFactory::class)->guard($guard);
    }
}

if (! function_exists('ws_back')) {
    /**
     * Create a new redirect response to the previous location.
     *
     * @param  int  $status
     * @param  array  $headers
     * @param  mixed  $fallback
     */
    function ws_back($status = 302, $headers = [], $fallback = false): RedirectResponse
    {
        return ws_app('redirect')->back($status, $headers, $fallback);
    }
}

if (! function_exists('ws_base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string  $path
     */
    function ws_base_path($path = ''): string
    {
        return ws_app()->basePath($path);
    }
}

if (! function_exists('ws_bcrypt')) {
    /**
     * Hash the given value against the bcrypt algorithm.
     *
     * @param  string  $value
     * @param  array  $options
     */
    function ws_bcrypt($value, $options = []): string
    {
        return ws_app('hash')->driver('bcrypt')->make($value, $options);
    }
}

if (! function_exists('ws_broadcast')) {
    /**
     * Begin broadcasting an event.
     *
     * @param  mixed  $event
     */
    function ws_broadcast($event = null): PendingBroadcast
    {
        return ws_app(BroadcastFactory::class)->event($event);
    }
}

if (! function_exists('ws_broadcast_if')) {
    /**
     * Begin broadcasting an event if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  mixed  $event
     */
    function ws_broadcast_if($boolean, $event = null): PendingBroadcast
    {
        if ($boolean) {
            return ws_app(BroadcastFactory::class)->event(ws_value($event));
        } else {
            return new FakePendingBroadcast;
        }
    }
}

if (! function_exists('ws_broadcast_unless')) {
    /**
     * Begin broadcasting an event unless the given condition is true.
     *
     * @param  bool  $boolean
     * @param  mixed  $event
     */
    function ws_broadcast_unless($boolean, $event = null): PendingBroadcast
    {
        if (! $boolean) {
            return ws_app(BroadcastFactory::class)->event(ws_value($event));
        } else {
            return new FakePendingBroadcast;
        }
    }
}

if (! function_exists('ws_cache')) {
    /**
     * Get / set the specified cache value.
     *
     * If an array is passed, we'll assume you want to put to the cache.
     *
     * @param  string|array<string, mixed>|null  $key  key|data
     * @param  mixed  $default  default|expiration|null
     * @return ($key is null ? \WpStarter\Cache\CacheManager : ($key is string ? mixed : bool))
     *
     * @throws \InvalidArgumentException
     */
    function ws_cache($key = null, $default = null)
    {
        if (is_null($key)) {
            return ws_app('cache');
        }

        if (is_string($key)) {
            return ws_app('cache')->get($key, $default);
        }

        if (! is_array($key)) {
            throw new InvalidArgumentException(
                'When setting a value in the cache, you must pass an array of key / value pairs.'
            );
        }

        return ws_app('cache')->put(key($key), array_first($key), ttl: $default);
    }
}

if (! function_exists('ws_config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array<string, mixed>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? \WpStarter\Config\Repository : ($key is string ? mixed : null))
     */
    function ws_config($key = null, $default = null)
    {
        if (is_null($key)) {
            return ws_app('config');
        }

        if (is_array($key)) {
            return ws_app('config')->set($key);
        }

        return ws_app('config')->get($key, $default);
    }
}

if (! function_exists('ws_config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string  $path
     */
    function ws_config_path($path = ''): string
    {
        return ws_app()->configPath($path);
    }
}

if (! function_exists('ws_context')) {
    /**
     * Get / set the specified context value.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return ($key is string ? mixed : \WpStarter\Log\Context\Repository)
     */
    function ws_context($key = null, $default = null)
    {
        $context = ws_app(ContextRepository::class);

        return match (true) {
            is_null($key) => $context,
            is_array($key) => $context->add($key),
            default => $context->get($key, $default),
        };
    }
}

if (! function_exists('ws_cookie')) {
    /**
     * Create a new cookie instance.
     *
     * @param  string|null  $name
     * @param  string|null  $value
     * @param  int  $minutes
     * @param  string|null  $path
     * @param  string|null  $domain
     * @param  bool|null  $secure
     * @param  bool  $httpOnly
     * @param  bool  $raw
     * @param  string|null  $sameSite
     * @return ($name is null ? \WpStarter\Cookie\CookieJar : \Symfony\Component\HttpFoundation\Cookie)
     */
    function ws_cookie($name = null, $value = null, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null): CookieJar|Cookie
    {
        $cookie = ws_app(CookieFactory::class);

        if (is_null($name)) {
            return $cookie;
        }

        return $cookie->make($name, $value, $minutes, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }
}

if (! function_exists('ws_csrf_field')) {
    /**
     * Generate a CSRF token form field.
     */
    function ws_csrf_field(): HtmlString
    {
        return new HtmlString('<input type="hidden" name="_token" value="'.ws_csrf_token().'" autocomplete="off">');
    }
}

if (! function_exists('ws_csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @throws \RuntimeException
     */
    function ws_csrf_token(): ?string
    {
        $session = ws_app('session');

        if (isset($session)) {
            return $session->token();
        }

        throw new RuntimeException('Application session store not set.');
    }
}

if (! function_exists('ws_database_path')) {
    /**
     * Get the database path.
     *
     * @param  string  $path
     */
    function ws_database_path($path = ''): string
    {
        return ws_app()->databasePath($path);
    }
}

if (! function_exists('ws_decrypt')) {
    /**
     * Decrypt the given value.
     *
     * @param  string  $value
     * @param  bool  $unserialize
     * @return mixed
     */
    function ws_decrypt($value, $unserialize = true)
    {
        return ws_app('encrypter')->decrypt($value, $unserialize);
    }
}

if (! function_exists('ws_defer')) {
    /**
     * Defer execution of the given callback.
     *
     * @return ($callback is null ? \WpStarter\Support\Defer\DeferredCallbackCollection : \WpStarter\Support\Defer\DeferredCallback)
     */
    function ws_defer(?callable $callback = null, ?string $name = null, bool $always = false): DeferredCallback|DeferredCallbackCollection
    {
        return \WpStarter\Support\defer($callback, $name, $always);
    }
}

if (! function_exists('ws_dispatch')) {
    /**
     * Dispatch a job to its appropriate handler.
     *
     * @param  mixed  $job
     * @return ($job is \Closure ? \WpStarter\Foundation\Bus\PendingClosureDispatch : \WpStarter\Foundation\Bus\PendingDispatch)
     */
    function ws_dispatch($job): PendingDispatch|PendingClosureDispatch
    {
        return $job instanceof Closure
            ? new PendingClosureDispatch(CallQueuedClosure::create($job))
            : new PendingDispatch($job);
    }
}

if (! function_exists('ws_dispatch_sync')) {
    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @param  mixed  $job
     * @param  mixed  $handler
     * @return mixed
     */
    function ws_dispatch_sync($job, $handler = null)
    {
        return ws_app(Dispatcher::class)->dispatchSync($job, $handler);
    }
}

if (! function_exists('ws_encrypt')) {
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     */
    function ws_encrypt($value, $serialize = true): string
    {
        return ws_app('encrypter')->encrypt($value, $serialize);
    }
}

if (! function_exists('ws_event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    function ws_event(...$args)
    {
        return ws_app('events')->dispatch(...$args);
    }
}

if (! function_exists('ws_fake') && class_exists(\Faker\Factory::class)) {
    /**
     * Get a faker instance.
     *
     * @param  string|null  $locale
     */
    function ws_fake($locale = null): \Faker\Generator
    {
        if (ws_app()->bound('config')) {
            $locale ??= ws_app('config')->get('app.faker_locale');
        }

        $locale ??= 'en_US';

        $abstract = \Faker\Generator::class.':'.$locale;

        if (! ws_app()->bound($abstract)) {
            ws_app()->singleton($abstract, fn () => \Faker\Factory::create($locale));
        }

        return ws_app()->make($abstract);
    }
}

if (! function_exists('ws_info')) {
    /**
     * Write some information to the log.
     *
     * @param  string  $message
     * @param  array  $context
     */
    function ws_info($message, $context = []): void
    {
        ws_app('log')->info($message, $context);
    }
}

if (! function_exists('ws_lang_path')) {
    /**
     * Get the path to the language folder.
     *
     * @param  string  $path
     */
    function ws_lang_path($path = ''): string
    {
        return ws_app()->langPath($path);
    }
}

if (! function_exists('ws_logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @param  string|null  $message
     * @return ($message is null ? \Psr\Log\LoggerInterface : null)
     */
    function ws_logger($message = null, array $context = []): ?LoggerInterface
    {
        if (is_null($message)) {
            return ws_app('log');
        }

        return ws_app('log')->debug($message, $context);
    }
}

if (! function_exists('ws_logs')) {
    /**
     * Get a log driver instance.
     *
     * @param  string|null  $driver
     * @return ($driver is null ? \WpStarter\Log\LogManager : \Psr\Log\LoggerInterface)
     */
    function ws_logs($driver = null): LoggerInterface|LogManager
    {
        return $driver ? ws_app('log')->driver($driver) : ws_app('log');
    }
}

if (! function_exists('ws_method_field')) {
    /**
     * Generate a form field to spoof the HTTP verb used by forms.
     *
     * @param  string  $method
     */
    function ws_method_field($method): HtmlString
    {
        return new HtmlString('<input type="hidden" name="_method" value="'.$method.'">');
    }
}

if (! function_exists('ws_mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @param  string  $manifestDirectory
     *
     * @throws \Exception
     */
    function ws_mix($path, $manifestDirectory = ''): HtmlString|string
    {
        return ws_app(Mix::class)(...func_get_args());
    }
}

if (! function_exists('ws_now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param  \DateTimeZone|\UnitEnum|string|null  $tz
     */
    function ws_now($tz = null): CarbonInterface
    {
        return Date::now(enum_value($tz));
    }
}

if (! function_exists('ws_old')) {
    /**
     * Retrieve an old input item.
     *
     * @param  string|null  $key
     * @param  \WpStarter\Database\Eloquent\Model|string|array|null  $default
     * @return string|array|null
     */
    function ws_old($key = null, $default = null)
    {
        return ws_app('request')->old($key, $default);
    }
}

if (! function_exists('ws_policy')) {
    /**
     * Get a policy instance for a given class.
     *
     * @param  object|string  $class
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    function ws_policy($class)
    {
        return ws_app(Gate::class)->getPolicyFor($class);
    }
}

if (! function_exists('ws_precognitive')) {
    /**
     * Handle a Precognition controller hook.
     *
     * @param  null|callable  $callable
     * @return mixed
     */
    function ws_precognitive($callable = null)
    {
        $callable ??= function () {
            //
        };

        $payload = $callable(function ($default, $precognition = null) {
            $response = ws_request()->isPrecognitive()
                ? ($precognition ?? $default)
                : $default;

            ws_abort(Router::toResponse(ws_request(), ws_value($response)));
        });

        if (ws_request()->isPrecognitive()) {
            ws_abort(204, headers: ['Precognition-Success' => 'true']);
        }

        return $payload;
    }
}

if (! function_exists('ws_public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param  string  $path
     */
    function ws_public_path($path = ''): string
    {
        return ws_app()->publicPath($path);
    }
}

if (! function_exists('ws_redirect')) {
    /**
     * Get an instance of the redirector.
     *
     * @param  string|null  $to
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return ($to is null ? \WpStarter\Routing\Redirector : \WpStarter\Http\RedirectResponse)
     */
    function ws_redirect($to = null, $status = 302, $headers = [], $secure = null): Redirector|RedirectResponse
    {
        if (is_null($to)) {
            return ws_app('redirect');
        }

        return ws_app('redirect')->to($to, $status, $headers, $secure);
    }
}

if (! function_exists('ws_report')) {
    /**
     * Report an exception.
     *
     * @param  \Throwable|string  $exception
     */
    function ws_report($exception): void
    {
        if (is_string($exception)) {
            $exception = new Exception($exception);
        }

        ws_app(ExceptionHandler::class)->report($exception);
    }
}

if (! function_exists('ws_report_if')) {
    /**
     * Report an exception if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Throwable|string  $exception
     */
    function ws_report_if($boolean, $exception): void
    {
        if ($boolean) {
            ws_report($exception);
        }
    }
}

if (! function_exists('ws_report_unless')) {
    /**
     * Report an exception unless the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Throwable|string  $exception
     */
    function ws_report_unless($boolean, $exception): void
    {
        if (! $boolean) {
            ws_report($exception);
        }
    }
}

if (! function_exists('ws_request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  list<string>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? \WpStarter\Http\Request : ($key is string ? mixed : array<string, mixed>))
     */
    function ws_request($key = null, $default = null)
    {
        if (is_null($key)) {
            return ws_app('request');
        }

        if (is_array($key)) {
            return ws_app('request')->only($key);
        }

        $value = ws_app('request')->__get($key);

        return is_null($value) ? ws_value($default) : $value;
    }
}

if (! function_exists('ws_rescue')) {
    /**
     * Catch a potential exception and return a default value.
     *
     * @template TValue
     * @template TFallback
     *
     * @param  callable(): TValue  $callback
     * @param  (callable(\Throwable): TFallback)|TFallback  $rescue
     * @param  bool|callable(\Throwable): bool  $report
     * @return TValue|TFallback
     */
    function ws_rescue(callable $callback, $rescue = null, $report = true)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            if (ws_value($report, $e)) {
                ws_report($e);
            }

            return ws_value($rescue, $e);
        }
    }
}

if (! function_exists('ws_resolve')) {
    /**
     * Resolve a service from the container.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>  $name
     * @return ($name is class-string<TClass> ? TClass : mixed)
     */
    function ws_resolve($name, array $parameters = [])
    {
        return ws_app($name, $parameters);
    }
}

if (! function_exists('ws_resource_path')) {
    /**
     * Get the path to the resources folder.
     *
     * @param  string  $path
     */
    function ws_resource_path($path = ''): string
    {
        return ws_app()->resourcePath($path);
    }
}

if (! function_exists('ws_response')) {
    /**
     * Return a new response from the application.
     *
     * @param  \WpStarter\Contracts\View\View|string|array|null  $content
     * @param  int  $status
     * @return ($content is null ? \WpStarter\Contracts\Routing\ResponseFactory : \WpStarter\Http\Response)
     */
    function ws_response($content = null, $status = 200, array $headers = []): ResponseFactory|IlluminateResponse
    {
        $factory = ws_app(ResponseFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content ?? '', $status, $headers);
    }
}

if (! function_exists('ws_route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  \BackedEnum|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    function ws_route($name, $parameters = [], $absolute = true): string
    {
        return ws_app('url')->route($name, $parameters, $absolute);
    }
}

if (! function_exists('ws_secure_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     */
    function ws_secure_asset($path): string
    {
        return ws_asset($path, true);
    }
}

if (! function_exists('ws_secure_url')) {
    /**
     * Generate a HTTPS url for the application.
     *
     * @param  string  $path
     * @param  mixed  $parameters
     * @return string
     */
    function ws_secure_url($path, $parameters = [])
    {
        return ws_url($path, $parameters, true);
    }
}

if (! function_exists('ws_session')) {
    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array<string, mixed>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? \WpStarter\Session\SessionManager : ($key is string ? mixed : null))
     */
    function ws_session($key = null, $default = null)
    {
        if (is_null($key)) {
            return ws_app('session');
        }

        if (is_array($key)) {
            return ws_app('session')->put($key);
        }

        return ws_app('session')->get($key, $default);
    }
}

if (! function_exists('ws_storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param  string  $path
     */
    function ws_storage_path($path = ''): string
    {
        return ws_app()->storagePath($path);
    }
}

if (! function_exists('ws_to_action')) {
    /**
     * Create a new redirect response to a controller action.
     *
     * @param  string|array  $action
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    function ws_to_action($action, $parameters = [], $status = 302, $headers = [])
    {
        return ws_redirect()->action($action, $parameters, $status, $headers);
    }
}

if (! function_exists('ws_to_route')) {
    /**
     * Create a new redirect response to a named route.
     *
     * @param  \BackedEnum|string  $route
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    function ws_to_route($route, $parameters = [], $status = 302, $headers = [])
    {
        return ws_redirect()->route($route, $parameters, $status, $headers);
    }
}

if (! function_exists('ws_today')) {
    /**
     * Create a new Carbon instance for the current date.
     *
     * @param  \DateTimeZone|\UnitEnum|string|null  $tz
     * @return \WpStarter\Support\Carbon
     */
    function ws_today($tz = null): CarbonInterface
    {
        return Date::today(enum_value($tz));
    }
}

if (! function_exists('ws_trans')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return ($key is null ? \WpStarter\Contracts\Translation\Translator : array|string)
     */
    function ws_trans($key = null, $replace = [], $locale = null): Translator|array|string
    {
        if (is_null($key)) {
            return ws_app('translator');
        }

        return ws_app('translator')->get($key, $replace, $locale);
    }
}

if (! function_exists('ws_trans_choice')) {
    /**
     * Translates the given message based on a count.
     *
     * @param  string  $key
     * @param  \Countable|int|float|array  $number
     * @param  string|null  $locale
     */
    function ws_trans_choice($key, $number, array $replace = [], $locale = null): string
    {
        return ws_app('translator')->choice($key, $number, $replace, $locale);
    }
}

if (! function_exists('ws___')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     */
    function ws___($key = null, $replace = [], $locale = null): string|array|null
    {
        if (is_null($key)) {
            return $key;
        }

        return ws_trans($key, $replace, $locale);
    }
}

if (! function_exists('ws_uri')) {
    /**
     * Generate a URI for the application.
     */
    function ws_uri(UriInterface|Stringable|array|string $uri, mixed $parameters = [], bool $absolute = true): Uri
    {
        return match (true) {
            is_array($uri) || str_contains($uri, '\\') => Uri::action($uri, $parameters, $absolute),
            str_contains($uri, '.') && Route::has($uri) => Uri::route($uri, $parameters, $absolute),
            default => Uri::of($uri),
        };
    }
}

if (! function_exists('ws_url')) {
    /**
     * Generate a URL for the application.
     *
     * @param  string|null  $path
     * @param  mixed  $parameters
     * @param  bool|null  $secure
     * @return ($path is null ? \WpStarter\Contracts\Routing\UrlGenerator : string)
     */
    function ws_url($path = null, $parameters = [], $secure = null): UrlGenerator|string
    {
        if (is_null($path)) {
            return ws_app(UrlGenerator::class);
        }

        return ws_app(UrlGenerator::class)->to($path, $parameters, $secure);
    }
}

if (! function_exists('ws_validator')) {
    /**
     * Create a new Validator instance.
     *
     * @return ($data is null ? \WpStarter\Contracts\Validation\Factory : \WpStarter\Contracts\Validation\Validator)
     */
    function ws_validator(?array $data = null, array $rules = [], array $messages = [], array $attributes = []): ValidatorContract|ValidationFactory
    {
        $factory = ws_app(ValidationFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data ?? [], $rules, $messages, $attributes);
    }
}

if (! function_exists('ws_view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  \WpStarter\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return ($view is null ? \WpStarter\Contracts\View\Factory : \WpStarter\Contracts\View\View)
     */
    function ws_view($view = null, $data = [], $mergeData = []): ViewFactory|ViewContract
    {
        $factory = ws_app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}
