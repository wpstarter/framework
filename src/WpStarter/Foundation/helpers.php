<?php

use WpStarter\Container\Container;
use WpStarter\Contracts\Auth\Factory as AuthFactory;
use WpStarter\Contracts\Broadcasting\Factory as BroadcastFactory;
use WpStarter\Contracts\Bus\Dispatcher;
use WpStarter\Contracts\Cookie\Factory as CookieFactory;
use WpStarter\Contracts\Debug\ExceptionHandler;
use WpStarter\Contracts\Routing\ResponseFactory;
use WpStarter\Contracts\Routing\UrlGenerator;
use WpStarter\Contracts\Support\Responsable;
use WpStarter\Contracts\Validation\Factory as ValidationFactory;
use WpStarter\Contracts\View\Factory as ViewFactory;
use WpStarter\Foundation\Bus\PendingClosureDispatch;
use WpStarter\Foundation\Bus\PendingDispatch;
use WpStarter\Foundation\Mix;
use WpStarter\Http\Exceptions\HttpResponseException;
use WpStarter\Queue\CallQueuedClosure;
use WpStarter\Support\Facades\Date;
use WpStarter\Support\HtmlString;
use Symfony\Component\HttpFoundation\Response;

if (! function_exists('ws_abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param  \Symfony\Component\HttpFoundation\Response|\WpStarter\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     * @param  array  $headers
     * @return never
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
     * @param  array  $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function ws_abort_if($boolean, $code, $message = '', array $headers = [])
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
     * @param  array  $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function ws_abort_unless($boolean, $code, $message = '', array $headers = [])
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
     * @return string
     */
    function ws_action($name, $parameters = [], $absolute = true)
    {
        return ws_app('url')->action($name, $parameters, $absolute);
    }
}

if (! function_exists('ws_app')) {
    /**
     * Get the available container instance.
     *
     * @param  string|null  $abstract
     * @param  array  $parameters
     * @return mixed|\WpStarter\Contracts\Foundation\Application
     */
    function ws_app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('ws_app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     * @return string
     */
    function ws_app_path($path = '')
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
     * @return string
     */
    function ws_asset($path, $secure = null)
    {
        return ws_app('url')->asset($path, $secure);
    }
}

if (! function_exists('ws_auth')) {
    /**
     * Get the available auth instance.
     *
     * @param  string|null  $guard
     * @return \WpStarter\Contracts\Auth\Factory|\WpStarter\Contracts\Auth\Guard
     */
    function ws_auth($guard = null)
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
     * @return \WpStarter\Http\RedirectResponse
     */
    function ws_back($status = 302, $headers = [], $fallback = false)
    {
        return ws_app('redirect')->back($status, $headers, $fallback);
    }
}

if (! function_exists('ws_base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string  $path
     * @return string
     */
    function ws_base_path($path = '')
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
     * @return string
     */
    function ws_bcrypt($value, $options = [])
    {
        return ws_app('hash')->driver('bcrypt')->make($value, $options);
    }
}

if (! function_exists('ws_broadcast')) {
    /**
     * Begin broadcasting an event.
     *
     * @param  mixed|null  $event
     * @return \WpStarter\Broadcasting\PendingBroadcast
     */
    function ws_broadcast($event = null)
    {
        return ws_app(BroadcastFactory::class)->event($event);
    }
}

if (! function_exists('ws_cache')) {
    /**
     * Get / set the specified cache value.
     *
     * If an array is passed, we'll assume you want to put to the cache.
     *
     * @param  dynamic  key|key,default|data,expiration|null
     * @return mixed|\WpStarter\Cache\CacheManager
     *
     * @throws \Exception
     */
    function ws_cache()
    {
        $arguments = func_get_args();

        if (empty($arguments)) {
            return ws_app('cache');
        }

        if (is_string($arguments[0])) {
            return ws_app('cache')->get(...$arguments);
        }

        if (! is_array($arguments[0])) {
            throw new Exception(
                'When setting a value in the cache, you must pass an array of key / value pairs.'
            );
        }

        return ws_app('cache')->put(key($arguments[0]), reset($arguments[0]), $arguments[1] ?? null);
    }
}

if (! function_exists('ws_config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed|\WpStarter\Config\Repository
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
     * @return string
     */
    function ws_config_path($path = '')
    {
        return ws_app()->configPath($path);
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
     * @return \WpStarter\Cookie\CookieJar|\Symfony\Component\HttpFoundation\Cookie
     */
    function ws_cookie($name = null, $value = null, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
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
     *
     * @return \WpStarter\Support\HtmlString
     */
    function ws_csrf_field()
    {
        return new HtmlString('<input type="hidden" name="_token" value="'.ws_csrf_token().'">');
    }
}

if (! function_exists('ws_csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function ws_csrf_token()
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
     * @return string
     */
    function ws_database_path($path = '')
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

if (! function_exists('ws_dispatch')) {
    /**
     * Dispatch a job to its appropriate handler.
     *
     * @param  mixed  $job
     * @return \WpStarter\Foundation\Bus\PendingDispatch
     */
    function ws_dispatch($job)
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

if (! function_exists('ws_dispatch_now')) {
    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param  mixed  $job
     * @param  mixed  $handler
     * @return mixed
     *
     * @deprecated Will be removed in a future Laravel version.
     */
    function ws_dispatch_now($job, $handler = null)
    {
        return ws_app(Dispatcher::class)->dispatchNow($job, $handler);
    }
}

if (! function_exists('ws_encrypt')) {
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     */
    function ws_encrypt($value, $serialize = true)
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

if (! function_exists('ws_info')) {
    /**
     * Write some information to the log.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    function ws_info($message, $context = [])
    {
        ws_app('log')->info($message, $context);
    }
}

if (! function_exists('ws_logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @param  string|null  $message
     * @param  array  $context
     * @return \WpStarter\Log\LogManager|null
     */
    function ws_logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return ws_app('log');
        }

        return ws_app('log')->debug($message, $context);
    }
}

if (! function_exists('ws_lang_path')) {
    /**
     * Get the path to the language folder.
     *
     * @param  string  $path
     * @return string
     */
    function ws_lang_path($path = '')
    {
        return ws_app('path.lang').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('ws_logs')) {
    /**
     * Get a log driver instance.
     *
     * @param  string|null  $driver
     * @return \WpStarter\Log\LogManager|\Psr\Log\LoggerInterface
     */
    function ws_logs($driver = null)
    {
        return $driver ? ws_app('log')->driver($driver) : ws_app('log');
    }
}

if (! function_exists('ws_method_field')) {
    /**
     * Generate a form field to spoof the HTTP verb used by forms.
     *
     * @param  string  $method
     * @return \WpStarter\Support\HtmlString
     */
    function ws_method_field($method)
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
     * @return \WpStarter\Support\HtmlString|string
     *
     * @throws \Exception
     */
    function ws_mix($path, $manifestDirectory = '')
    {
        return ws_app(Mix::class)(...func_get_args());
    }
}

if (! function_exists('ws_now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param  \DateTimeZone|string|null  $tz
     * @return \WpStarter\Support\Carbon
     */
    function ws_now($tz = null)
    {
        return Date::now($tz);
    }
}

if (! function_exists('ws_old')) {
    /**
     * Retrieve an old input item.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function ws_old($key = null, $default = null)
    {
        return ws_app('request')->old($key, $default);
    }
}

if (! function_exists('ws_public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param  string  $path
     * @return string
     */
    function ws_public_path($path = '')
    {
        return ws_app()->make('path.public').($path ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : $path);
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
     * @return \WpStarter\Routing\Redirector|\WpStarter\Http\RedirectResponse
     */
    function ws_redirect($to = null, $status = 302, $headers = [], $secure = null)
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
     * @return void
     */
    function ws_report($exception)
    {
        if (is_string($exception)) {
            $exception = new Exception($exception);
        }

        ws_app(ExceptionHandler::class)->report($exception);
    }
}

if (! function_exists('ws_request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return \WpStarter\Http\Request|string|array|null
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
     * @param  callable  $callback
     * @param  mixed  $rescue
     * @param  bool  $report
     * @return mixed
     */
    function ws_rescue(callable $callback, $rescue = null, $report = true)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            if ($report) {
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
     * @param  string  $name
     * @param  array  $parameters
     * @return mixed
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
     * @return string
     */
    function ws_resource_path($path = '')
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
     * @param  array  $headers
     * @return \WpStarter\Http\Response|\WpStarter\Contracts\Routing\ResponseFactory
     */
    function ws_response($content = '', $status = 200, array $headers = [])
    {
        $factory = ws_app(ResponseFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}

if (! function_exists('ws_route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  array|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function ws_route($name, $parameters = [], $absolute = true)
    {
        return ws_app('url')->route($name, $parameters, $absolute);
    }
}

if (! function_exists('ws_secure_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @return string
     */
    function ws_secure_asset($path)
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
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed|\WpStarter\Session\Store|\WpStarter\Session\SessionManager
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
     * @return string
     */
    function ws_storage_path($path = '')
    {
        return ws_app('path.storage').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('ws_today')) {
    /**
     * Create a new Carbon instance for the current date.
     *
     * @param  \DateTimeZone|string|null  $tz
     * @return \WpStarter\Support\Carbon
     */
    function ws_today($tz = null)
    {
        return Date::today($tz);
    }
}

if (! function_exists('ws_trans')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return \WpStarter\Contracts\Translation\Translator|string|array|null
     */
    function ws_trans($key = null, $replace = [], $locale = null)
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
     * @param  \Countable|int|array  $number
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string
     */
    function ws_trans_choice($key, $number, array $replace = [], $locale = null)
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
     * @return string|array|null
     */
    function ws___($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return $key;
        }

        return ws_trans($key, $replace, $locale);
    }
}

if (! function_exists('ws_url')) {
    /**
     * Generate a url for the application.
     *
     * @param  string|null  $path
     * @param  mixed  $parameters
     * @param  bool|null  $secure
     * @return \WpStarter\Contracts\Routing\UrlGenerator|string
     */
    function ws_url($path = null, $parameters = [], $secure = null)
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
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return \WpStarter\Contracts\Validation\Validator|\WpStarter\Contracts\Validation\Factory
     */
    function ws_validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $factory = ws_app(ValidationFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data, $rules, $messages, $customAttributes);
    }
}

if (! function_exists('ws_view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  \WpStarter\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \WpStarter\Contracts\View\View|\WpStarter\Contracts\View\Factory
     */
    function ws_view($view = null, $data = [], $mergeData = [])
    {
        $factory = ws_app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}
