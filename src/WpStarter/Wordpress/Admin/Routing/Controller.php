<?php

namespace WpStarter\Wordpress\Admin\Routing;

use WpStarter\Wordpress\Admin\View\Layout;

abstract class Controller extends \WpStarter\Routing\Controller
{
    /**
     * @var Menu
     */
    protected $menu;

    public function setMenu($menu)
    {
        $this->menu = $menu;
        return $this;
    }


}