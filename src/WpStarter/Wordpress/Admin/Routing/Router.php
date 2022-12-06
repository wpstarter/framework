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
use WpStarter\Routing\MiddlewareNameResolver;
use WpStarter\Routing\Pipeline;
use WpStarter\Routing\SortedMiddleware;
use WpStarter\Support\Collection;
use WpStarter\Support\Str;
use WpStarter\Support\Stringable;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use WpStarter\Wordpress\Admin\Routing\Events\MenuMatched;

class Router
{
    /**
     * @var MenuCollection|Menu[]
     */
    protected $menus;
    protected $container;
    protected $events;

    /**
     * The currently dispatched menu instance.
     *
     * @var \WpStarter\Wordpress\Admin\Routing\Menu|null
     */
    protected $current;

    /**
     * The request currently being dispatched.
     *
     * @var \WpStarter\Http\Request
     */
    protected $currentRequest;

    /**
     * All of the short-hand keys for middlewares.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * All of the middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The priority-sorted list of middleware.
     *
     * Forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    public $middlewarePriority = [];

    /**
     * The route group attribute stack.
     *
     * @var array
     */
    protected $groupStack = [];

    public function __construct(Dispatcher $events, Container $container = null)
    {
        $this->menus = new MenuCollection();
        $this->container = $container;
        $this->events = $events;
    }

    public function newMenu($slug, $controller, $capability = 'read', $title ='' , $page_title = '', $icon = '', $position = null)
    {

        if ($this->hasGroupStack()) {
            if(is_string($controller)){
                $controller=$this->prependGroupNamespace($controller);
            }
        }
        $menu = (new Menu($slug, $controller, $capability, $title ,$page_title, $icon, $position))
            ->setRouter($this)
            ->setContainer($this->container);
        $menu->middleware($this->getLastGroupMiddleware());

        return $menu;
    }
    protected function prependGroupNamespace($class)
    {
        $group = end($this->groupStack);
        return isset($group['namespace']) && strpos($class, '\\') !== 0
            ? $group['namespace'].'\\'.$class : $class;
    }
    protected function getLastGroupMiddleware(){
        if($this->hasGroupStack()){
            $last=end($this->groupStack);
            return $last['middleware']??[];
        }
        return [];
    }

    public function add($slug, $controller, $capability = 'read', $title ='' , $page_title = '', $icon = '', $position = null)
    {
        $this->addMenu($menu = $this->newMenu($slug, $controller, $capability, $title ,$page_title, $icon, $position));
        return $menu;
    }


    function addMenu(Menu $menu)
    {
        $this->menus->add($menu);
        return $this;
    }

    function register()
    {
        foreach ($this->menus as $menu) {
            if(!$menu->slug){
                continue;
            }
            $menu->initialize();
            if (!$menu->parent) {
                $menu->hookSuffix = add_menu_page(
                    $menu->pageTitle,
                    $menu->title,
                    $menu->capability,
                    $menu->slug,
                    function () use ($menu) {
                        echo $this->getContent($menu);
                    },
                    $menu->icon,
                    $menu->position
                );
            } else {
                $menu->hookSuffix = add_submenu_page(
                    $menu->parent,
                    $menu->pageTitle,
                    $menu->title,
                    $menu->capability,
                    $menu->slug,
                    function () use ($menu) {
                        echo $this->getContent($menu);
                    },
                    $menu->position
                );
            }
            $this->menus->addByHook($menu->hookSuffix, $menu);
        }
    }

    protected function getContent(Menu $menu)
    {
        if($response=$menu->getResponse()){
            return $response->getContent();
        }
        return '';
    }


    
    /**
     * Dispatch the request to the application.
     *
     * @param \WpStarter\Http\Request $request
     * @param $hook
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatch(Request $request, $hook)
    {
        $this->currentRequest = $request;

        return $this->dispatchToMenu($request, $hook);
    }

    /**
     * Dispatch the request to a menu and return the response.
     *
     * @param \WpStarter\Http\Request $request
     * @param string $hook
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatchToMenu(Request $request, $hook)
    {
        if ($menu = $this->findMenu($hook)) {
            return $this->runMenu($request, $menu);
        }
    }

    /**
     * Find the menu matching a given hook.
     *
     * @param string $hook
     * @return Menu
     */
    protected function findMenu($hook)
    {
        $this->current = $menu = $this->menus->findByHook($hook);

        return $menu;
    }

    /**
     * Return the response for the given menu.
     *
     * @param \WpStarter\Http\Request $request
     * @param \WpStarter\Wordpress\Admin\Routing\Menu $menu
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function runMenu(Request $request, Menu $menu)
    {
        $this->events->dispatch(new MenuMatched($menu, $request));
        $response = $this->prepareResponse($request,
            $this->runMenuWithinStack($menu, $request)
        );
        //Cary the response to output later
        $menu->setResponse($response);
        return $response;
    }

    /**
     * Run the given menu within a Stack "onion" instance.
     *
     * @param \WpStarter\Wordpress\Admin\Routing\Menu $menu
     * @param \WpStarter\Http\Request $request
     * @return mixed
     */
    protected function runMenuWithinStack(Menu $menu, Request $request)
    {
        $shouldSkipMiddleware = $this->container->bound('middleware.disable') &&
            $this->container->make('middleware.disable') === true;
        $menu->setRequest($request);
        $middleware = $shouldSkipMiddleware ? [] : $this->gatherMenuMiddleware($menu);

        return (new Pipeline($this->container))
            ->send($request)
            ->through($middleware)
            ->then(function ($request) use ($menu) {
                return $this->prepareResponse(
                    $request, $menu->run()
                );
            });
    }

    /**
     * Gather the middleware for the given menu with resolved class names.
     *
     * @param \WpStarter\Wordpress\Admin\Routing\Menu $menu
     * @return array
     */
    public function gatherMenuMiddleware(Menu $menu)
    {
        $computedMiddleware = $menu->gatherMiddleware();

        $excluded = ws_collect($menu->excludedMiddleware())->map(function ($name) {
            return (array)MiddlewareNameResolver::resolve($name, $this->middleware, $this->middlewareGroups);
        })->flatten()->values()->all();

        $middleware = ws_collect($computedMiddleware)->map(function ($name) {
            return (array)MiddlewareNameResolver::resolve($name, $this->middleware, $this->middlewareGroups);
        })->flatten()->reject(function ($name) use ($excluded) {
            if (empty($excluded)) {
                return false;
            }

            if ($name instanceof \Closure) {
                return false;
            }

            if (in_array($name, $excluded, true)) {
                return true;
            }

            if (!class_exists($name)) {
                return false;
            }

            $reflection = new \ReflectionClass($name);

            return ws_collect($excluded)->contains(function ($exclude) use ($reflection) {
                return class_exists($exclude) && $reflection->isSubclassOf($exclude);
            });
        })->values();
        return $this->sortMiddleware($middleware);
    }

    /**
     * Sort the given middleware by priority.
     *
     * @param \WpStarter\Support\Collection $middlewares
     * @return array
     */
    protected function sortMiddleware(Collection $middlewares)
    {
        return (new SortedMiddleware($this->middlewarePriority, $middlewares))->all();
    }

    /**
     * Create a response instance from the given value.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function prepareResponse($request, $response)
    {
        return static::toResponse($request, $response);
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


    /**
     * Get all of the defined middleware short-hand names.
     *
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Register a short-hand name for a middleware.
     *
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function aliasMiddleware($name, $class)
    {
        $this->middleware[$name] = $class;

        return $this;
    }

    /**
     * Check if a middlewareGroup with the given name exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasMiddlewareGroup($name)
    {
        return array_key_exists($name, $this->middlewareGroups);
    }

    /**
     * Get all of the defined middleware groups.
     *
     * @return array
     */
    public function getMiddlewareGroups()
    {
        return $this->middlewareGroups;
    }

    /**
     * Register a group of middleware.
     *
     * @param  string  $name
     * @param  array  $middleware
     * @return $this
     */
    public function middlewareGroup($name, array $middleware)
    {
        $this->middlewareGroups[$name] = $middleware;

        return $this;
    }

    /**
     * Add a middleware to the beginning of a middleware group.
     *
     * If the middleware is already in the group, it will not be added again.
     *
     * @param  string  $group
     * @param  string  $middleware
     * @return $this
     */
    public function prependMiddlewareToGroup($group, $middleware)
    {
        if (isset($this->middlewareGroups[$group]) && ! in_array($middleware, $this->middlewareGroups[$group])) {
            array_unshift($this->middlewareGroups[$group], $middleware);
        }

        return $this;
    }

    /**
     * Add a middleware to the end of a middleware group.
     *
     * If the middleware is already in the group, it will not be added again.
     *
     * @param  string  $group
     * @param  string  $middleware
     * @return $this
     */
    public function pushMiddlewareToGroup($group, $middleware)
    {
        if (! array_key_exists($group, $this->middlewareGroups)) {
            $this->middlewareGroups[$group] = [];
        }

        if (! in_array($middleware, $this->middlewareGroups[$group])) {
            $this->middlewareGroups[$group][] = $middleware;
        }

        return $this;
    }

    /**
     * Flush the router's middleware groups.
     *
     * @return $this
     */
    public function flushMiddlewareGroups()
    {
        $this->middlewareGroups = [];

        return $this;
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  array  $attributes
     * @param  \Closure|string  $routes
     * @return void
     */
    public function group(array $attributes, $routes)
    {
        $this->updateGroupStack($attributes);

        // Once we have updated the group stack, we'll load the provided routes and
        // merge in the group's attributes when the routes are created. After we
        // have created the routes, we will pop the attributes off the stack.
        $this->loadRoutes($routes);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array  $attributes
     * @return void
     */
    protected function updateGroupStack(array $attributes)
    {
        if ($this->hasGroupStack()) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given array with the last group stack.
     *
     * @param  array  $new
     * @return array
     */
    public function mergeWithLastGroup($new)
    {
        return RouteGroup::merge($new, end($this->groupStack));
    }

    /**
     * Load the provided routes.
     *
     * @param  \Closure|string  $routes
     * @return void
     */
    protected function loadRoutes($routes)
    {
        if ($routes instanceof \Closure) {
            $routes($this);
        } else {
            (new RouteFileRegistrar($this))->register($routes);
        }
    }

    /**
     * Determine if the router currently has a group stack.
     *
     * @return bool
     */
    public function hasGroupStack()
    {
        return ! empty($this->groupStack);
    }

    /**
     * Get the current group stack for the router.
     *
     * @return array
     */
    public function getGroupStack()
    {
        return $this->groupStack;
    }

    /**
     * Get the request currently being dispatched.
     *
     * @return \WpStarter\Http\Request
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * Get the currently dispatched menu instance.
     *
     * @return Menu|null
     */
    public function getCurrentMenu()
    {
        return $this->current();
    }

    /**
     * Get the currently dispatched menu instance.
     *
     * @return Menu|null
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * @return Menu[]|MenuCollection
     */
    public function menus(){
        return $this->menus;
    }

    /**
     * Find menu by slug
     * @param $slug
     * @return Menu|null
     */
    public function menu($slug){
        return $this->menus->find($slug);
    }
    /**
     * Remove any duplicate middleware from the given array.
     *
     * @param array $middleware
     * @return array
     */
    public static function uniqueMiddleware(array $middleware)
    {
        $seen = [];
        $result = [];

        foreach ($middleware as $value) {
            $key = \is_object($value) ? \spl_object_id($value) : $value;

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $value;
            }
        }

        return $result;
    }
    /**
     * Dynamically handle calls into the router instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method === 'middleware') {
            return (new RouteRegistrar($this))->attribute($method, is_array($parameters[0]) ? $parameters[0] : $parameters);
        }

        return (new RouteRegistrar($this))->attribute($method, array_key_exists(0, $parameters) ? $parameters[0] : true);
    }

}