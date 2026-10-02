<?php

namespace WpStarter\Foundation\Http\Middleware;

use WpStarter\Container\Container;
use WpStarter\Foundation\Routing\PrecognitionCallableDispatcher;
use WpStarter\Foundation\Routing\PrecognitionControllerDispatcher;
use WpStarter\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use WpStarter\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;

class HandlePrecognitiveRequests
{
    /**
     * The container instance.
     *
     * @var \WpStarter\Container\Container
     */
    protected $container;

    /**
     * Create a new middleware instance.
     *
     * @param  \WpStarter\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Closure  $next
     * @return \WpStarter\Http\Response
     */
    public function handle($request, $next)
    {
        if (! $request->isAttemptingPrecognition()) {
            return $this->appendVaryHeader($request, $next($request));
        }

        $bindings = $this->container->getBindings();
        $callableBinding = $bindings[CallableDispatcherContract::class] ?? null;
        $controllerBinding = $bindings[ControllerDispatcherContract::class] ?? null;

        $this->prepareForPrecognition($request);

        return ws_tap($next($request), function ($response) use ($request, $callableBinding, $controllerBinding) {
            $response->headers->set('Precognition', 'true');

            $this->appendVaryHeader($request, $response);

            $this->restoreDispatchers($callableBinding, $controllerBinding);
        });
    }

    /**
     * Prepare to handle a precognitive request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return void
     */
    protected function prepareForPrecognition($request)
    {
        $request->attributes->set('precognitive', true);

        $this->container->bind(CallableDispatcherContract::class, fn ($app) => new PrecognitionCallableDispatcher($app));
        $this->container->bind(ControllerDispatcherContract::class, fn ($app) => new PrecognitionControllerDispatcher($app));
    }

    /**
     * Append the appropriate "Vary" header to the given response.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \WpStarter\Http\Response  $response
     * @return \WpStarter\Http\Response
     */
    protected function appendVaryHeader($request, $response)
    {
        return ws_tap($response, fn () => $response->headers->set('Vary', implode(', ', array_filter([
            $response->headers->get('Vary'),
            'Precognition',
        ]))));
    }

    /**
     * Restore the original route dispatcher bindings.
     *
     * @param  array|null  $callableBinding
     * @param  array|null  $controllerBinding
     * @return void
     */
    protected function restoreDispatchers($callableBinding, $controllerBinding)
    {
        if ($callableBinding) {
            $this->container->bind(CallableDispatcherContract::class, $callableBinding['concrete'], $callableBinding['shared']);
        }

        if ($controllerBinding) {
            $this->container->bind(ControllerDispatcherContract::class, $controllerBinding['concrete'], $controllerBinding['shared']);
        }
    }
}
