<?php

namespace WpStarter\Wordpress\Admin\Routing;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use WpStarter\Container\Container;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Contracts\Support\Arrayable;
use WpStarter\Contracts\Support\Jsonable;
use WpStarter\Contracts\Support\Responsable;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Http\JsonResponse;
use WpStarter\Http\Request;
use WpStarter\Routing\Route;
use WpStarter\Support\Stringable;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

/**
 * @method Menu match($methods, $uri, $action)
 */
class Router extends \WpStarter\Routing\Router
{
    /**
     * @var MenuCollection
     */
    protected $routes;

    /**
     * @param Dispatcher $events
     * @param Container|null $container
     */
    public function __construct(Dispatcher $events, Container $container = null)
    {
        parent::__construct($events, $container);
        $this->routes = new MenuCollection();
    }

    public function newRoute($methods, $uri, $action)
    {
        return (new Menu($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    protected function runRoute(Request $request, Route $route)
    {
        $response = parent::runRoute($request, $route);
        $route->setResponse($response);
        return $response;
    }

    function dispatch(Request $request)
    {
        return parent::dispatch($request);
    }

    public function add($slug, $controller, $capability = '', $title ='' , $page_title = '', $icon = '', $position = null)
    {
        $route=$this->match(['GET','POST'],$slug,$controller)
            ->title($title)
            ->pageTitle($page_title)
            ->iconUrl($icon)
            ->position($position);
        if($capability){
            $route->capability($capability);
        }
        return $route;
    }

    function register()
    {
        foreach ($this->routes->getRoutes() as $menu) {
            /**
             * @var Menu $menu
             */
            if(!$menu->uri()){
                continue;
            }
            $menu->initialize();
            if (!$menu->getAction('parent')) {
                $menu->hookSuffix = add_menu_page(
                    $menu->getAction('page_title'),
                    $menu->getAction('title'),
                    $menu->getAction('capability'),
                    $menu->uri(),
                    function () use ($menu) {
                        echo $this->getContent($menu);
                    },
                    $menu->getAction('icon'),
                    $menu->getAction('position')
                );
            } else {
                $menu->hookSuffix = add_submenu_page(
                    $menu->getAction('parent'),
                    $menu->getAction('page_title'),
                    $menu->getAction('title'),
                    $menu->getAction('capability'),
                    $menu->uri(),
                    function () use ($menu) {
                        echo $this->getContent($menu);
                    },
                    $menu->getAction('position')
                );
            }
        }
        $this->routes->refreshNameLookups();
        $this->routes->refreshActionLookups();
    }
    protected function getContent(Menu $menu)
    {
        if($response=$menu->getResponse()){
            return $response->getContent();
        }
        return '';
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
            $response = new Response($response->__toString(), 200, ['Content-Type' => 'text/html']);
        } elseif (!$response instanceof SymfonyResponse &&
            ($response instanceof Arrayable ||
                $response instanceof Jsonable ||
                $response instanceof \ArrayObject ||
                $response instanceof \JsonSerializable ||
                $response instanceof \stdClass ||
                is_array($response))) {
            $response = new JsonResponse($response);
        } elseif (!$response instanceof SymfonyResponse) {
            $response = new Response($response, 200, ['Content-Type' => 'text/html']);
        }

        if ($response->getStatusCode() === SymfonyResponse::HTTP_NOT_MODIFIED) {
            $response->setNotModified();
        }

        return $response->prepare($request);
    }

    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if ($method === 'middleware') {
            return (new RouteRegistrar($this))->attribute($method, is_array($parameters[0]) ? $parameters[0] : $parameters);
        }

        return (new RouteRegistrar($this))->attribute($method, array_key_exists(0, $parameters) ? $parameters[0] : true);
    }

}
