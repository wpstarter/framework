<?php

namespace WpStarter\Wordpress\View;

use WpStarter\Contracts\Support\Renderable;
use WpStarter\Wordpress\Http\Response;

abstract class Component implements Renderable
{
    protected $data = [];
    /**
     * @var Response|Response\Content|Response\Page|Response\Shortcode
     */
    protected $response;

    function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    function getResponse()
    {
        return $this->response;
    }

    function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Component boot call on kernel handle
     */
    function boot()
    {

    }

    /**
     * Call before render
     */
    function mount()
    {

    }
}