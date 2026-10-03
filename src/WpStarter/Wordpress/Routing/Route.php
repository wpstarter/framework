<?php

namespace WpStarter\Wordpress\Routing;

use WpStarter\Routing\Matching\HostValidator;
use WpStarter\Routing\Matching\MethodValidator;
use WpStarter\Routing\Matching\SchemeValidator;
use WpStarter\Routing\Matching\UriValidator;
use WpStarter\Wordpress\Routing\Matching\ShortcodeValidator;

class Route extends \WpStarter\Routing\Route
{
    public static $validators;
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
