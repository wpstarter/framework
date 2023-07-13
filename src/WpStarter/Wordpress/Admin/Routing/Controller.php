<?php

namespace WpStarter\Wordpress\Admin\Routing;

use WpStarter\Container\BoundMethod;
use WpStarter\Http\Request;
use WpStarter\Support\Arr;
use WpStarter\Support\Str;
use WpStarter\Wordpress\Admin\View\Layout;

abstract class Controller extends \WpStarter\Routing\Controller
{

    protected $defaultAction='index';
    protected $actionKey = ['action','action2'];
    protected $ignoreActions=["-1"];
    protected $actionsMap=[];

    /**
     * Map resource actions
     * @return $this
     */
    protected function resource(){
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
        $this->actionsMap=array_merge($this->actionsMap,$maps);
        return $this;
    }

    /**
     * Get action from request
     * @return string
     */
    protected function getActionFromRequest($request)
    {
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

    protected function getActionMethod($request)
    {
        $action = $this->getActionFromRequest($request);
        $requestMethod = $request->method();
        $defaultMethod = Str::camel(strtolower($requestMethod) . '_' . $action);

        $key = strtolower($requestMethod . ':' . $action);
        return $this->actionsMap[$key] ?? $defaultMethod;
    }

    public function __invoke(...$parameters)
    {
        $method=$this->getActionMethod(ws_app('request'));
        return ws_app()->call([$this,$method],$parameters);
    }


}