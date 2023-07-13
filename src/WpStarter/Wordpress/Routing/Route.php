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

    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }

        // To match the route, we will use a chain of responsibility pattern with the
        // validator implementations. We will spin through each one making sure it
        // passes and then we will know if the route as a whole matches request.
        return static::$validators = [
            new UriValidator, new MethodValidator,
            new SchemeValidator, new HostValidator,
            new ShortcodeValidator(),
        ];
    }

}
