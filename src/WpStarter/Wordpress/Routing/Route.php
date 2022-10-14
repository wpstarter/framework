<?php

namespace WpStarter\Wordpress\Routing;

class Route extends \WpStarter\Routing\Route
{
    /**
     * @var \WpStarter\Http\Response
     */
    protected $response;
    function run()
    {
        if(is_null($this->response)) {
            $this->response = Router::toResponse($this->container['request'],parent::run());
        }
        return $this->response;
    }
    function getContent(){
        if($this->response){
            return $this->response->getContent();
        }
        return '';
    }

}