<?php

namespace WpStarter\Wordpress\Routing;

class RouteHook
{
    /**
     * Set hook to route
     * @return \Closure
     */
    function hook(){
        return function($hook, $priority=10){
            $this->action['hook']=$hook;
            $this->action['priority']=$priority;
            return $this;
        };
    }

}
