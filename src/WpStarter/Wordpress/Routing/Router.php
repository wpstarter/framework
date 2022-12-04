<?php

namespace WpStarter\Wordpress\Routing;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use WpStarter\Container\Container;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Contracts\Support\Arrayable;
use WpStarter\Contracts\Support\Jsonable;
use WpStarter\Contracts\Support\Responsable;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Http\JsonResponse;
use WpStarter\Http\Request;
use WpStarter\Routing\Router as BaseRouter;
use WpStarter\Support\Stringable;
use ArrayObject;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use WpStarter\Wordpress\Http\ShortcodeResponse;

class Router extends BaseRouter
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @param Dispatcher $events
     * @param Container|null $container
     */
    public function __construct(Dispatcher $events, Container $container = null)
    {
        parent::__construct($events, $container);
        $this->routes = new RouteCollection();
    }

    public function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    function registerShortcodes(Request $request)
    {
        foreach ($this->routes->get($request->getMethod()) as $route) {
            add_shortcode($route->uri(), function () use ($route) {
                return $route->getContent();
            });
        }
    }

    /**
     * Static version of prepareResponse.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function toResponse($request, $response)
    {
        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }

        if ($response instanceof PsrResponseInterface) {
            $response = (new HttpFoundationFactory)->createResponse($response);
        } elseif ($response instanceof Model && $response->wasRecentlyCreated) {
            $response = new JsonResponse($response, 201);
        } elseif ($response instanceof Stringable) {
            $response = new ShortcodeResponse($response->__toString(), 200, ['Content-Type' => 'text/html']);
        } elseif (!$response instanceof SymfonyResponse &&
            ($response instanceof Arrayable ||
                $response instanceof Jsonable ||
                $response instanceof ArrayObject ||
                $response instanceof JsonSerializable ||
                $response instanceof \stdClass ||
                is_array($response))) {
            $response = new JsonResponse($response);
        } elseif (!$response instanceof SymfonyResponse) {
            $response = new ShortcodeResponse($response, 200, ['Content-Type' => 'text/html']);
        }

        if ($response->getStatusCode() === SymfonyResponse::HTTP_NOT_MODIFIED) {
            $response->setNotModified();
        }

        return $response->prepare($request);
    }
}