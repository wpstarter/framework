<?php

namespace WpStarter\Wordpress\Admin\Routing;

use WpStarter\Wordpress\Admin\View\Layout;

abstract class Controller extends \WpStarter\Routing\Controller
{
    /**
     * @var Menu
     */
    protected $menu;
    /**
     * @var Layout
     */
    protected $layout;

    public function setMenu($menu)
    {
        $this->menu = $menu;
        $this->layout = $this->menu->layout();
        return $this;
    }

    public function url($params=[], $action=null){
        return $this->menu->url($params, $action);
    }

}