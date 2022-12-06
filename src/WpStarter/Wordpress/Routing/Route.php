<?php

namespace WpStarter\Wordpress\Routing;

class Route extends \WpStarter\Routing\Route
{
    /**
     * @var \WpStarter\Http\Response
     */
    protected $response;

    public function setResponse($response){
        $this->response=$response;
        return $this;
    }
    public function getResponse(){
        return $this->response;
    }

    function getContent()
    {
        if ($this->response) {
            return $this->response->getContent();
        }
        return '';
    }

}