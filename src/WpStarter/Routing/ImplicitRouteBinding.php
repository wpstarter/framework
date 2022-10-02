<?php

namespace WpStarter\Routing;

use WpStarter\Contracts\Routing\UrlRoutable;
use WpStarter\Database\Eloquent\ModelNotFoundException;
use WpStarter\Database\Eloquent\SoftDeletes;
use WpStarter\Support\Reflector;
use WpStarter\Support\Str;

class ImplicitRouteBinding
{
    /**
     * Resolve the implicit route bindings for the given route.
     *
     * @param  \WpStarter\Container\Container  $container
     * @param  \WpStarter\Routing\Route  $route
     * @return void
     *
     * @throws \WpStarter\Database\Eloquent\ModelNotFoundException
     */
    public static function resolveForRoute($container, $route)
    {
        $parameters = $route->parameters();

        foreach ($route->signatureParameters(UrlRoutable::class) as $parameter) {
            if (! $parameterName = static::getParameterName($parameter->getName(), $parameters)) {
                continue;
            }

            $parameterValue = $parameters[$parameterName];

            if ($parameterValue instanceof UrlRoutable) {
                continue;
            }

            $instance = $container->make(Reflector::getParameterClassName($parameter));

            $parent = $route->parentOfParameter($parameterName);

            $routeBindingMethod = $route->allowsTrashedBindings() && in_array(SoftDeletes::class, ws_class_uses_recursive($instance))
                        ? 'resolveSoftDeletableRouteBinding'
                        : 'resolveRouteBinding';

            if ($parent instanceof UrlRoutable && ($route->enforcesScopedBindings() || array_key_exists($parameterName, $route->bindingFields()))) {
                $childRouteBindingMethod = $route->allowsTrashedBindings() && in_array(SoftDeletes::class, ws_class_uses_recursive($instance))
                            ? 'resolveSoftDeletableChildRouteBinding'
                            : 'resolveChildRouteBinding';

                if (! $model = $parent->{$childRouteBindingMethod}(
                    $parameterName, $parameterValue, $route->bindingFieldFor($parameterName)
                )) {
                    throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
                }
            } elseif (! $model = $instance->{$routeBindingMethod}($parameterValue, $route->bindingFieldFor($parameterName))) {
                throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
            }

            $route->setParameter($parameterName, $model);
        }
    }

    /**
     * Return the parameter name if it exists in the given parameters.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return string|null
     */
    protected static function getParameterName($name, $parameters)
    {
        if (array_key_exists($name, $parameters)) {
            return $name;
        }

        if (array_key_exists($snakedName = Str::snake($name), $parameters)) {
            return $snakedName;
        }
    }
}
