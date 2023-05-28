<?php

namespace WpStarter\Wordpress\PluginsLoader;

use WpStarter\Http\Request;
use WpStarter\Routing\RouteUri;

class Rule
{
    /**
     * The URI pattern the rule responds to.
     *
     * @var string
     */
    public $uri;

    /**
     * The HTTP methods the rule responds to.
     *
     * @var array
     */
    public $methods;

    /**
     * The rule action array.
     *
     * @var array
     */
    public $action;

    /**
     * Indicates whether the rule is a fallback rule.
     *
     * @var bool
     */
    public $isFallback = false;
    protected $whitelist=[];
    protected $blacklist=[];
    public function __construct($methods, $uri, $action){
        $this->uri = $uri;
        $this->methods = (array) $methods;
        $this->action = $action;

        if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }
    }

    function run($plugins){
        if(!$this->whitelist && !$this->blacklist){
            return $plugins;
        }
        return array_filter($plugins,function($plugin){
            if($this->whitelist){
                return in_array($plugin,$this->whitelist);
            }else{
                return !in_array($plugin,$this->blacklist);
            }
        });

    }

    /**
     * Get or set the domain for the rule.
     *
     * @param  string|null  $domain
     * @return $this|string|null
     */
    public function domain($domain = null)
    {
        if (is_null($domain)) {
            return $this->getDomain();
        }

        $parsed = RouteUri::parse($domain);

        $this->action['domain'] = $parsed->uri;


        return $this;
    }

    /**
     * Get the domain defined for the rule.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return isset($this->action['domain'])
            ? str_replace(['http://', 'https://'], '', $this->action['domain']) : null;
    }

    /**
     * Get the URI associated with the rule.
     *
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Mark this rule as a fallback rule.
     *
     * @return $this
     */
    public function fallback()
    {
        $this->isFallback = true;

        return $this;
    }

    /**
     * Set the fallback value.
     *
     * @param  bool  $isFallback
     * @return $this
     */
    public function setFallback($isFallback)
    {
        $this->isFallback = $isFallback;

        return $this;
    }
    /**
     * Get the HTTP verbs the rule responds to.
     *
     * @return array
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * Determine if the rule matches a given request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  bool  $includingMethod
     * @return bool
     */
    public function matches(Request $request, $includingMethod = true)
    {

        if($request->path()===$this->uri()){
            return true;
        }

        return true;
    }

    /**
     * Load only given plugins
     * @param ...$plugins
     * @return $this
     */
    function only(...$plugins){
        if(is_array($plugins[0])){
            $plugins=$plugins[0];
        }
        $this->whitelist=$plugins;
        return $this;
    }

    /**
     * Load all plugins excerpt given
     * @param ...$plugins
     * @return $this
     */
    function excerpt(...$plugins){
        if(is_array($plugins[0])){
            $plugins=$plugins[0];
        }
        $this->blacklist=$plugins;
        return $this;
    }
}
