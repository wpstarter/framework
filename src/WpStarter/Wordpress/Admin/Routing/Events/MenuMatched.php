<?php

namespace WpStarter\Wordpress\Admin\Routing\Events;

use WpStarter\Wordpress\Admin\Routing\Menu;

class MenuMatched
{
    /**
     * The menu instance.
     *
     * @var Menu
     */
    public $menu;

    /**
     * The request instance.
     *
     * @var \WpStarter\Http\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param \WpStarter\Wordpress\Admin\Routing\Menu $route
     * @param \WpStarter\Http\Request $request
     * @return void
     */
    public function __construct($menu, $request)
    {
        $this->menu = $menu;
        $this->request = $request;
    }
}