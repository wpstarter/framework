<?php

namespace WpStarter\Wordpress\Http\Request;

trait SupportNotFoundExceptionTrace
{
    protected $routeNotFoundException;
    /**
     * Check Not found response raised by route matching
     * @return bool
     */
    public function isNotFoundHttpExceptionFromRoute(){
        return $this->routeNotFoundException;
    }

    /**
     * @param $flag
     * @return $this
     */
    public function setRouteNotFoundHttpException($flag=true){
        $this->routeNotFoundException=$flag;
        return $this;
    }
}
