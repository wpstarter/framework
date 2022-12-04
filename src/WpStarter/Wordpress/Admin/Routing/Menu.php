<?php

namespace WpStarter\Wordpress\Admin\Routing;

use WpStarter\Container\Container;
use WpStarter\Support\Arr;
use WpStarter\Support\Str;
use WpStarter\Wordpress\Admin\View\Layout;

class Menu
{
    protected $actionKey = ['action','action2'];
    /**
     * @var \WpStarter\Http\Request
     */
    protected $request;
    /**
     * @var \WpStarter\Http\Response
     */
    protected $response;
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var Router
     */
    protected $router;
    protected $controller;

    protected $middleware = [];
    protected $excludedMiddleware = [];
    protected $computedMiddleware;
    protected $methods = [];
    protected $layout;

    public $pageTitle;
    public $title;
    public $capability;
    public $slug;
    public $callback;
    public $icon;
    public $position;
    public $parent;
    public $hookSuffix;

    public function __construct($slug, $callback, $capability = 'read', $title ='' ,$page_title = '', $icon = '', $position = null)
    {
        $this->pageTitle = $page_title;
        $this->title = $title;
        $this->capability = $capability;
        $this->slug = $slug;
        $this->callback = $callback;
        $this->icon = $icon;
        $this->position = $position;
        $this->layout = new Layout();
    }

    public function initialize(){
        if(!$this->title){
            $this->title=Str::title($this->slug);
        }
        if(!$this->pageTitle){
            $this->pageTitle=$this->title;
        }
        $this->layout()->title($this->pageTitle);
    }

    public function addSubMenu($slug, $callback, $capability = 'read', $title='', $page_title = '', $position = null)
    {
        $menu = $this->router->newMenu($slug, $callback, $capability, $title, $page_title, $callback, '', $position);
        $menu->parent = $this->slug;
        $this->router->addMenu($menu);
        return $menu;
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setActionKey($actionKey)
    {
        $this->actionKey = $actionKey;
        return $this;
    }
    public function getActionKey($single=true){
        if($single&&is_array($this->actionKey)){
            return Arr::first($this->actionKey);
        }
        return $this->actionKey;
    }

    function layout(){
        return $this->layout;
    }

    function url($params=[],$action=null){
        if(!is_array($params)){
            $params=[];
        }
        $url=menu_page_url($this->slug,false);
        $params[$this->getActionKey()]=$action;
        $params=array_filter($params, function ($p) {
            return ! is_null($p);
        });
        return add_query_arg($params,$url);
    }

    /**
     * Map resource actions
     * @return $this
     */
    public function resource(){
        $maps=[
            'get:index'=>'index',
            'get:create'=>'create',
            'post:store'=>'store',
            'get:show'=>'show',
            'get:edit'=>'edit',
            'put:update'=>'update',
            'patch:update'=>'update',
            'delete:destroy'=>'destroy',
        ];
        $this->methods=array_merge($this->methods,$maps);
        return $this;
    }

    /**
     * Set controller method for action
     * @param $requestMethod
     * @param $action
     * @param $controllerMethod
     * @return $this
     */
    public function action($requestMethod, $action, $controllerMethod)
    {
        $key = strtolower($requestMethod . ':' . $action);
        $this->methods[$key] = $controllerMethod;
        return $this;
    }

    public function getAction()
    {
        $request = $this->getRequest();
        foreach ((array)$this->actionKey as $key) {
            if ($action = $request->input($key)) {
                return $action;
            }
        }
        return null;
    }

    protected function getController()
    {
        if (! $this->controller) {
            $class = $this->callback;
            $this->controller = $this->container->make(ltrim($class, '\\'));
            if (method_exists($this->controller, 'setMenu')) {
                return $this->controller->setMenu($this);
            }
        }

        return $this->controller;
    }

    protected function getControllerMethod()
    {
        $action = $this->getAction();
        if (!$action) {
            $action = 'index';
        }
        $requestMethod = $this->request->method();
        $defaultAction = Str::camel(strtolower($requestMethod) . '_' . $action);
        $key = strtolower($requestMethod . ':' . $action);
        return $this->methods[$key] ?? $defaultAction;
    }


    protected function controllerDispatcher()
    {
        return new ControllerDispatcher($this->container);
    }

    protected function runController()
    {
        if(!$this->callback){
            throw new \RuntimeException("Please register a callback for menu");
        }
        if (is_string($this->callback)) {
            return $this->controllerDispatcher()->dispatch($this, $this->getController(), $this->getControllerMethod());
        }
        return $this->runCallable();
    }

    /**
     * Run the route action and return the response.
     *
     * @return mixed
     */
    protected function runCallable()
    {
        $callable = $this->callback;
        return $callable();
    }

    function run()
    {
        if (is_null($this->response)) {
            $request = $this->container['request'];
            $this->layout()->loadMessages();
            $this->response = Router::toResponse($request, $this->runController());
        }
        return $this->response;
    }

    function getContent()
    {
        if ($this->response) {
            return $this->response->getContent();
        }
        return '';
    }

    /**
     * @param mixed $page_title
     * @return Menu
     */
    public function pageTitle($page_title)
    {
        $this->pageTitle = $page_title;
        return $this;
    }

    /**
     * @param mixed $menu_title
     * @return Menu
     */
    public function title($menu_title)
    {
        $this->title = $menu_title;
        return $this;
    }

    /**
     * @param mixed $capability
     * @return Menu
     */
    public function capability($capability)
    {
        $this->capability = $capability;
        return $this;
    }

    /**
     * @param mixed $menu_slug
     * @return Menu
     */
    public function slug($menu_slug)
    {
        $this->slug = $menu_slug;
        return $this;
    }

    /**
     * @param mixed|string $callback
     * @return Menu
     */
    public function callback(mixed $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param mixed|string $icon_url
     * @return Menu
     */
    public function iconUrl(mixed $icon_url)
    {
        $this->icon = $icon_url;
        return $this;
    }

    /**
     * @param mixed|null $position
     * @return Menu
     */
    public function position(mixed $position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @param mixed $parent
     * @return Menu
     */
    public function parent($parent)
    {
        $this->parent = $parent;
        return $this;
    }


    /**
     * Get all middleware, including the ones from the controller.
     *
     * @return array
     */
    public function gatherMiddleware()
    {
        if (!is_null($this->computedMiddleware)) {
            return $this->computedMiddleware;
        }

        $this->computedMiddleware = [];

        return $this->computedMiddleware = Router::uniqueMiddleware(array_merge(
            $this->middleware(), $this->controllerMiddleware()
        ));
    }

    /**
     * Get or set the middlewares attached to the route.
     *
     * @param array|string|null $middleware
     * @return $this|array
     */
    public function middleware($middleware = null)
    {
        if (is_null($middleware)) {
            return (array)($this->middleware ?? []);
        }

        if (!is_array($middleware)) {
            $middleware = func_get_args();
        }

        foreach ($middleware as $index => $value) {
            $middleware[$index] = (string)$value;
        }

        $this->middleware = array_merge(
            (array)($this->middleware ?? []), $middleware
        );

        return $this;
    }

    /**
     * Get the middleware should be removed from the route.
     *
     * @return array
     */
    public function excludedMiddleware()
    {
        return (array)($this->excludedMiddleware ?? []);
    }

    /**
     * Get the middleware for the route's controller.
     *
     * @return array
     */
    public function controllerMiddleware()
    {
        if (! is_string($this->callback)) {
            return [];
        }

        return $this->controllerDispatcher()->getMiddleware(
            $this->getController(), $this->getControllerMethod()
        );
    }

}