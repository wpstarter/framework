<?php

namespace WpStarter\Wordpress\Plugins;

use WpStarter\Http\Request;
use WpStarter\Wordpress\Plugins\Loader\Rule;
use WpStarter\Wordpress\Plugins\Loader\RulesCollection;

class Loader
{
    protected $rules;
    protected $current;
    protected $request;
    protected static $instance;
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    public function __construct()
    {
        $this->rules=new RulesCollection();
    }
    function run(){
        add_filter('option_active_plugins',[$this,'filterPlugins']);
    }
    function filterPlugins($plugins){
        $request=$this->getRequest();
        $rule=$this->findRule($request);
        if($rule){
            $plugins=$rule->run($plugins);
        }
        return $plugins;
    }

    /**
     * Get the currently dispatched rule instance.
     *
     * @return Rule|null
     */
    public function current()
    {
        return $this->current;
    }
    /**
     * Add a rule to the underlying rule collection.
     *
     * @param  array|string  $methods
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function addRule($methods, $uri, $action)
    {
        return $this->rules->add($this->createRule($methods, $uri, $action));
    }

    /**
     * Create a new rule instance.
     *
     * @param  array|string  $methods
     * @param  string|\Closure  $uri
     * @param  mixed  $action
     * @return Rule
     */
    protected function createRule($methods, $uri, $action)
    {
        if(!is_array($action)){
            $action = ['uses' => $action];
        }
        return new Rule($methods,$uri,$action);
    }
    /**
     * Register a new GET rule with the PluginsLoader.
     *
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function get($uri, $action = null)
    {
        return $this->addRule(['GET', 'HEAD'], $uri, $action);
    }

    /**
     * Register a new POST rule with the PluginsLoader.
     *
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function post($uri, $action = null)
    {
        return $this->addRule('POST', $uri, $action);
    }

    /**
     * Register a new PUT rule with the PluginsLoader.
     *
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function put($uri, $action = null)
    {
        return $this->addRule('PUT', $uri, $action);
    }

    /**
     * Register a new PATCH rule with the PluginsLoader.
     *
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function patch($uri, $action = null)
    {
        return $this->addRule('PATCH', $uri, $action);
    }

    /**
     * Register a new DELETE rule with the PluginsLoader.
     *
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function delete($uri, $action = null)
    {
        return $this->addRule('DELETE', $uri, $action);
    }

    /**
     * Register a new OPTIONS rule with the PluginsLoader.
     *
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function options($uri, $action = null)
    {
        return $this->addRule('OPTIONS', $uri, $action);
    }

    /**
     * Register a new rule responding to all verbs.
     *
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function any($uri, $action = null)
    {
        return $this->addRule(self::$verbs, $uri, $action);
    }
    /**
     * Register a new rule with the given verbs.
     *
     * @param  array|string  $methods
     * @param  string|\Closure  $uri
     * @param  array|string|callable|null  $action
     * @return Rule
     */
    public function match($methods, $uri, $action = null)
    {
        return $this->addRule(array_map('strtoupper', (array) $methods), $uri, $action);
    }
    /**
     * Find the rule matching a given request.
     *
     */
    protected function findRule($request)
    {
        $this->current = $rule = $this->rules->match($request);

        return $rule;
    }

    public function setRequest($request){
        $this->request=$request;
        return $this;
    }
    public function getRequest(){
        if(!$this->request){
            $this->request=Request::capture();
        }
        return $this->request;
    }
    /**
     * @return static
     */
    public static function getInstance(){
        if(!static::$instance){
            static::$instance=new static();
        }
        return static::$instance;
    }
}
