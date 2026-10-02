<?php

namespace WpStarter\Foundation\Http\Middleware;

use Closure;
use WpStarter\Container\Container;
use WpStarter\Http\Request;
use WpStarter\Support\Defer\DeferredCallbackCollection;
use Symfony\Component\HttpFoundation\Response;

class InvokeDeferredCallbacks
{
    /**
     * Handle the incoming request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Invoke the deferred callbacks.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        Container::getInstance()
            ->make(DeferredCallbackCollection::class)
            ->invokeWhen(fn ($callback) => $response->getStatusCode() < 400 || $callback->always);
    }
}
