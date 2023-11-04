<?php

namespace WpStarter\Wordpress\Routing;

class RouteHook
{
    /**
     * Set hook to route
     * @return \Closure
     */
    function hook(){
        return function($hook, $priority=null){
            $this->action['hook']=$hook.($priority?(':'.$priority):'');
            return $this;
        };
    }

}
