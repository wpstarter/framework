<?php

namespace WpStarter\Routing\Middleware;

use Closure;
use WpStarter\Contracts\Routing\Registrar;
use WpStarter\Database\Eloquent\ModelNotFoundException;

class SubstituteBindings
{
    /**
     * The router instance.
     *
     * @var \WpStarter\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new bindings substitutor.
     *
     * @param  \WpStarter\Contracts\Routing\Registrar  $router
     * @return void
     */
    public function __construct(Registrar $router)
    {
        $this->router = $router;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->router->substituteBindings($route = $request->route());

            $this->router->substituteImplicitBindings($route);
        } catch (ModelNotFoundException $exception) {
            if ($route->getMissing()) {
                return $route->getMissing()($request, $exception);
            }

            throw $exception;
        }

        return $next($request);
    }
}
