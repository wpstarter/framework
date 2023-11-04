<?php

namespace WpStarter\Wordpress\Routing\Matching;

use WpStarter\Http\Request;
use WpStarter\Routing\Matching\ValidatorInterface;
use WpStarter\Routing\Route;

class HookValidator implements ValidatorInterface
{
    public function matches(Route $route, Request $request)
    {
        if($hook=$route->getAction('hook')){
            list($hook,$priority)=$this->parseHook($hook);
            global $wp_filter;
            $hookPassed= (did_action($hook)||did_filter($hook));
            $currentPriority=null;
            if(isset($wp_filter[$hook])) {
                $currentPriority=$wp_filter[$hook]->current_priority();
            }
            $priorityPassed=$currentPriority===$priority;
            return $hookPassed && $priorityPassed;
        }
        return true;
    }
    protected function parseHook($hook){
        if(!is_array($hook)){
            $hook=explode(':',$hook);
        }
        if(!isset($hook[0])){
            $hook[0]='init';
        }
        if(!isset($hook[1])){
            $hook[1]=10;
        }
        $hook[1]=intval($hook[1]);
        return $hook;
    }
}
