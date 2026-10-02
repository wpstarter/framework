<?php

namespace WpStarter\Auth\Middleware;

use WpStarter\Database\Eloquent\Contracts\Model as ModelContract;
use Closure;
use WpStarter\Contracts\Auth\Access\Gate;
use WpStarter\Support\Collection;

use function WpStarter\Support\enum_value;

class Authorize
{
    /**
     * The gate instance.
     *
     * @var \WpStarter\Contracts\Auth\Access\Gate
     */
    protected $gate;

    /**
     * Create a new middleware instance.
     *
     * @param  \WpStarter\Contracts\Auth\Access\Gate  $gate
     */
    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    /**
     * Specify the ability and models for the middleware.
     *
     * @param  \UnitEnum|string  $ability
     * @param  string  ...$models
     * @return string
     */
    public static function using($ability, ...$models)
    {
        return static::class.':'.implode(',', [enum_value($ability), ...$models]);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ability
     * @param  array|null  ...$models
     * @return mixed
     *
     * @throws \WpStarter\Auth\AuthenticationException
     * @throws \WpStarter\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, $ability, ...$models)
    {
        $this->gate->authorize($ability, $this->getGateArguments($request, $models));

        return $next($request);
    }

    /**
     * Get the arguments parameter for the gate.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  array|null  $models
     * @return array
     */
    protected function getGateArguments($request, $models)
    {
        if (is_null($models)) {
            return [];
        }

        return (new Collection($models))
            ->map(fn ($model) => $model instanceof ModelContract ? $model : $this->getModel($request, $model))
            ->all();
    }

    /**
     * Get the model to authorize.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  string  $model
     * @return \WpStarter\Database\Eloquent\Contracts\Model|string
     */
    protected function getModel($request, $model)
    {
        if ($this->isClassName($model)) {
            return trim($model);
        }

        return $request->route($model, null) ??
            ((preg_match("/^['\"](.*)['\"]$/", trim($model), $matches)) ? $matches[1] : null);
    }

    /**
     * Checks if the given string looks like a fully-qualified class name.
     *
     * @param  string  $value
     * @return bool
     */
    protected function isClassName($value)
    {
        return str_contains($value, '\\');
    }
}
