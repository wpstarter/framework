<?php

namespace WpStarter\Wordpress\Admin\Routing;

use ReflectionFunction;
use WpStarter\Container\Container;
use WpStarter\Http\Exceptions\HttpResponseException;
use WpStarter\Routing\RouteDependencyResolverTrait;
use WpStarter\Support\Arr;
use WpStarter\Support\Str;
use WpStarter\Wordpress\Admin\View\Layout;

class Menu
{
    use RouteDependencyResolverTrait;
    protected $defaultAction='index';
    protected $actionKey = ['action','action2'];
    protected $ignoreActions=["-1"];
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

    protected $hide=false;

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
            $this->title=Str::headline($this->slug);
        }
        if(!$this->pageTitle){
            $this->pageTitle=$this->title;
        }
        $this->layout()->title($this->pageTitle);
        $this->layout()->setNoticeManager($this->container['wp.admin.notice']);
    }

    /**
     * @param $callback
     * @return $this
     */
    public function build($callback){
        $callback($this);
        return $this;
    }

    /**
     * Create new group for children menu
     * @param $callback
     * @return $this
     */
    public function group($callback){
        $this->router->group(['parent'=>$this->slug],$callback);
        return $this;
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

        $params[$this->getActionKey()]=$action;

        return ws_admin_url($this->slug,$params);
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

    /**
     * Get action from request
     * @return string
     */
    public function getAction()
    {
        $request = $this->getRequest();
        foreach ((array)$this->actionKey as $key) {
            if ($action = $request->input($key)) {
                if(!in_array($action,$this->ignoreActions)) {
                    return $action;
                }
            }
        }
        return $this->defaultAction;
    }
    public function ignoreActions(...$actions){
        $this->ignoreActions=is_array($actions[0])?$actions[0]:$actions;
        return $this;
    }

    protected function getController()
    {
        if (! $this->controller) {
            $class = $this->parseControllerCallback()[0];
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
        $requestMethod = $this->request->method();
        $defaultMethod = Str::camel(strtolower($requestMethod) . '_' . $action);
        if($defaultMethod==='getIndex'){
            $registeredMethod=$this->parseControllerCallback()[1]??'';
            if($registeredMethod){
                $defaultMethod=$registeredMethod;
            }
        }

        $key = strtolower($requestMethod . ':' . $action);
        return $this->methods[$key] ?? $defaultMethod;
    }

    protected function parseControllerCallback()
    {
        if(is_array($this->callback)){
            return $this->callback;
        }
        return Str::parseCallback($this->callback);
    }

    /**
     * Check if callback is a controller
     * @return boolean
     */
    protected function isControllerCallback(){
        if(!$this->callback){
            throw new \RuntimeException("Please register a callback for menu");
        }
        return is_string($this->callback) || (is_array($this->callback) && count($this->callback)===2);
    }
    protected function controllerDispatcher()
    {
        return new ControllerDispatcher($this->container);
    }

    protected function runController()
    {
        return $this->controllerDispatcher()->dispatch($this, $this->getController(), $this->getControllerMethod());
    }

    /**
     * Run the route action and return the response.
     *
     * @return mixed
     */
    protected function runCallable()
    {
        $callable = $this->callback;
        return $callable(...array_values($this->resolveMethodDependencies(
            [], new ReflectionFunction($callable)
        )));
    }

    function run()
    {
        try {
            if ($this->isControllerCallback()) {
                return $this->runController();
            }

            return $this->runCallable();
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }

    public function setResponse($response){
        $this->response=$response;
        return $this;
    }
    public function getResponse(){
        return $this->response;
    }

    /**
     * Hide the menu
     * @return $this
     */
    public function hide($hide=true){
        if($hide) {
            $this->hide = $this->parent;
            $this->parent = 'ws-admin-hidden-menu';
        }else{
            if($this->parent ==='ws-admin-hidden-menu'){
                $this->parent = $this->hide;
                $this->hide=null;
            }
        }
        return $this;
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
    public function callback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param mixed|string $icon_url
     * @return Menu
     */
    public function iconUrl($icon_url)
    {
        $this->icon = $icon_url;
        return $this;
    }

    /**
     * @param mixed|null $position
     * @return Menu
     */
    public function position($position)
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
