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
            return did_action($hook)||did_filter($hook);
        }
        return false;
    }
}
