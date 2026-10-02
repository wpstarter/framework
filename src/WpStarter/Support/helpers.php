<?php

use Carbon\CarbonInterval;
use WpStarter\Contracts\Support\DeferringDisplayableValue;
use WpStarter\Contracts\Support\Htmlable;
use WpStarter\Database\Eloquent\Contracts\Model as ModelContract;
use WpStarter\Support\Arr;
use WpStarter\Support\Env;
use WpStarter\Support\Fluent;
use WpStarter\Support\HigherOrderTapProxy;
use WpStarter\Support\Once;
use WpStarter\Support\Onceable;
use WpStarter\Support\Optional;
use WpStarter\Support\Sleep;
use WpStarter\Support\Str;
use WpStarter\Support\Stringable as SupportStringable;

if (! function_exists('ws_append_config')) {
    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param  array  $array
     */
    function ws_append_config(array $array): array
    {
        $start = 9999;

        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $start++;

                $array[$start] = Arr::pull($array, $key);
            }
        }

        return $array;
    }
}

if (! function_exists('ws_blank')) {
    /**
     * Determine if the given value is "blank".
     *
     * @phpstan-assert-if-false !=null|'' $value
     *
     * @phpstan-assert-if-true !=numeric|bool $value
     *
     * @param  mixed  $value
     */
    function ws_blank($value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof ModelContract) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        if ($value instanceof Stringable) {
            return trim((string) $value) === '';
        }

        return empty($value);
    }
}

if (! function_exists('ws_class_basename')) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     */
    function ws_class_basename($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if (! function_exists('ws_class_uses_recursive')) {
    /**
     * Returns all traits used by a class, its parent classes and trait of their traits.
     *
     * @param  object|string  $class
     * @return array<string, string>
     */
    function ws_class_uses_recursive($class): array
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_reverse(class_parents($class) ?: []) + [$class => $class] as $class) {
            $results += ws_trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}

if (! function_exists('ws_e')) {
    /**
     * Encode HTML special characters in a string.
     *
     * @param  \WpStarter\Contracts\Support\DeferringDisplayableValue|\WpStarter\Contracts\Support\Htmlable|\BackedEnum|string|int|float|null  $value
     * @param  bool  $doubleEncode
     */
    function ws_e($value, $doubleEncode = true): string
    {
        if ($value instanceof DeferringDisplayableValue) {
            $value = $value->resolveDisplayableValue();
        }

        if ($value instanceof Htmlable) {
            return $value->toHtml() ?? '';
        }

        if ($value instanceof BackedEnum) {
            $value = $value->value;
        }

        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}

if (! function_exists('ws_env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function ws_env($key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (! function_exists('ws_filled')) {
    /**
     * Determine if a value is "filled".
     *
     * @phpstan-assert-if-true !=null|'' $value
     *
     * @phpstan-assert-if-false !=numeric|bool $value
     *
     * @param  mixed  $value
     */
    function ws_filled($value): bool
    {
        return ! ws_blank($value);
    }
}

if (! function_exists('ws_fluent')) {
    /**
     * Create a Fluent object from the given value.
     *
     * @param  iterable|object|null  $value
     */
    function ws_fluent($value = null): Fluent
    {
        return new Fluent($value ?? []);
    }
}

if (! function_exists('ws_literal')) {
    /**
     * Return a new literal or anonymous object using named arguments.
     *
     * @return mixed
     */
    function ws_literal(...$arguments)
    {
        if (count($arguments) === 1 && array_is_list($arguments)) {
            return $arguments[0];
        }

        return (object) $arguments;
    }
}

if (! function_exists('ws_object_get')) {
    /**
     * Get an item from an object using "dot" notation.
     *
     * @template TValue of object
     *
     * @param  TValue  $object
     * @param  string|null  $key
     * @param  mixed  $default
     * @return ($key is empty ? TValue : mixed)
     */
    function ws_object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) === '') {
            return $object;
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_object($object) || ! isset($object->{$segment})) {
                return ws_value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if (! function_exists('wpstarter_cloud')) {
    /**
     * Determine if the application is running on Laravel Cloud.
     */
    function wpstarter_cloud(): bool
    {
        return ($_ENV['LARAVEL_CLOUD'] ?? false) === '1' ||
            ($_SERVER['LARAVEL_CLOUD'] ?? false) === '1';
    }
}

if (! function_exists('ws_once')) {
    /**
     * Ensures a callable is only called once, and returns the result on subsequent calls.
     *
     * @template  TReturnType
     *
     * @param  callable(): TReturnType  $callback
     * @return TReturnType
     */
    function ws_once(callable $callback)
    {
        $onceable = Onceable::tryFromTrace(
            debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2),
            $callback,
        );

        return $onceable ? Once::instance()->value($onceable) : call_user_func($callback);
    }
}

if (! function_exists('ws_optional')) {
    /**
     * Provide access to optional objects.
     *
     * @template TValue
     * @template TReturn
     *
     * @param  TValue  $value
     * @param  (callable(TValue): TReturn)|null  $callback
     * @return ($callback is null ? \WpStarter\Support\Optional : ($value is null ? null : TReturn))
     */
    function ws_optional($value = null, ?callable $callback = null)
    {
        if (is_null($callback)) {
            return new Optional($value);
        } elseif (! is_null($value)) {
            return $callback($value);
        }
    }
}

if (! function_exists('ws_preg_replace_array')) {
    /**
     * Replace a given pattern with each value in the array in sequentially.
     *
     * @param  string  $pattern
     * @param  array  $replacements
     * @param  string  $subject
     */
    function ws_preg_replace_array($pattern, array $replacements, $subject): string
    {
        return preg_replace_callback($pattern, function () use (&$replacements) {
            return array_shift($replacements);
        }, $subject);
    }
}

if (! function_exists('ws_retry')) {
    /**
     * Retry an operation a given number of times.
     *
     * @template TValue
     *
     * @param  int|array<int, int>  $times
     * @param  callable(int): TValue  $callback
     * @param  CarbonInterval|int|\Closure(int, \Throwable): CarbonInterval|int  $sleepMilliseconds
     * @param  (callable(\Throwable): bool)|null  $when
     * @return TValue
     *
     * @throws \Throwable
     */
    function ws_retry($times, callable $callback, $sleepMilliseconds = 0, $when = null)
    {
        $attempts = 0;

        $backoff = [];

        if (is_array($times)) {
            $backoff = $times;

            $times = count($times) + 1;
        }

        beginning:
        $attempts++;
        $times--;

        try {
            return $callback($attempts);
        } catch (Throwable $e) {
            if ($times < 1 || ($when && ! $when($e))) {
                throw $e;
            }

            $sleepMilliseconds = $backoff[$attempts - 1] ?? $sleepMilliseconds;

            if ($sleepMilliseconds) {
                $duration = ws_value($sleepMilliseconds, $attempts, $e);

                $duration instanceof CarbonInterval
                    ? Sleep::usleep($duration->totalMicroseconds)
                    : Sleep::usleep($duration * 1000);
            }

            goto beginning;
        }
    }
}

if (! function_exists('ws_str')) {
    /**
     * Get a new stringable object from the given string.
     *
     * @param  string|null  $string
     * @return ($string is null ? object : \WpStarter\Support\Stringable)
     */
    function ws_str($string = null)
    {
        if (func_num_args() === 0) {
            return new class
            {
                public function __call($method, $parameters)
                {
                    return Str::$method(...$parameters);
                }

                public function __toString()
                {
                    return '';
                }
            };
        }

        return new SupportStringable($string);
    }
}

if (! function_exists('ws_tap')) {
    /**
     * Call the given Closure with the given value then return the value.
     *
     * @template TValue
     *
     * @param  TValue  $value
     * @param  (callable(TValue): mixed)|null  $callback
     * @return ($callback is null ? \WpStarter\Support\HigherOrderTapProxy : TValue)
     */
    function ws_tap($value, $callback = null)
    {
        if (is_null($callback)) {
            return new HigherOrderTapProxy($value);
        }

        $callback($value);

        return $value;
    }
}

if (! function_exists('ws_throw_if')) {
    /**
     * Throw the given exception if the given condition is true.
     *
     * @template TValue
     * @template TParams of mixed
     * @template TException of \Throwable
     * @template TExceptionValue of TException|class-string<TException>|string
     *
     * @param  TValue  $condition
     * @param  Closure(TParams): TExceptionValue|TExceptionValue  $exception
     * @param  TParams  ...$parameters
     * @return ($condition is true ? never : ($condition is non-empty-mixed ? never : TValue))
     *
     * @throws TException
     */
    function ws_throw_if($condition, $exception = 'RuntimeException', ...$parameters)
    {
        if ($condition) {
            if ($exception instanceof Closure) {
                $exception = $exception(...$parameters);
            }

            if (is_string($exception) && class_exists($exception)) {
                $exception = new $exception(...$parameters);
            }

            throw is_string($exception) ? new RuntimeException($exception) : $exception;
        }

        return $condition;
    }
}

if (! function_exists('ws_throw_unless')) {
    /**
     * Throw the given exception unless the given condition is true.
     *
     * @template TValue
     * @template TParams of mixed
     * @template TException of \Throwable
     * @template TExceptionValue of TException|class-string<TException>|string
     *
     * @param  TValue  $condition
     * @param  Closure(TParams): TExceptionValue|TExceptionValue  $exception
     * @param  TParams  ...$parameters
     * @return ($condition is false ? never : ($condition is non-empty-mixed ? TValue : never))
     *
     * @throws TException
     */
    function ws_throw_unless($condition, $exception = 'RuntimeException', ...$parameters)
    {
        ws_throw_if(! $condition, $exception, ...$parameters);

        return $condition;
    }
}

if (! function_exists('ws_trait_uses_recursive')) {
    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param  object|string  $trait
     * @return array<string, string>
     */
    function ws_trait_uses_recursive($trait): array
    {
        $traits = class_uses($trait) ?: [];

        foreach ($traits as $trait) {
            $traits += ws_trait_uses_recursive($trait);
        }

        return $traits;
    }
}

if (! function_exists('ws_transform')) {
    /**
     * Transform the given value if it is present.
     *
     * @template TValue
     * @template TReturn
     * @template TDefault
     *
     * @param  TValue  $value
     * @param  callable(TValue): TReturn  $callback
     * @param  TDefault|callable(TValue): TDefault  $default
     * @return ($value is empty ? TDefault : TReturn)
     */
    function ws_transform($value, callable $callback, $default = null)
    {
        if (ws_filled($value)) {
            return $callback($value);
        }

        if (is_callable($default)) {
            return $default($value);
        }

        return $default;
    }
}

if (! function_exists('ws_windows_os')) {
    /**
     * Determine whether the current environment is Windows based.
     */
    function ws_windows_os(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }
}

if (! function_exists('ws_with')) {
    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @template TValue
     * @template TReturn
     *
     * @param  TValue  $value
     * @param  (callable(TValue): (TReturn))|null  $callback
     * @return ($callback is null ? TValue : TReturn)
     */
    function ws_with($value, ?callable $callback = null)
    {
        return is_null($callback) ? $value : $callback($value);
    }
}
