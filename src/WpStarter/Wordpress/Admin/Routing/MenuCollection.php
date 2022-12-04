<?php

namespace WpStarter\Wordpress\Admin\Routing;


class MenuCollection implements \Countable, \IteratorAggregate
{
    protected $menus = [];
    protected $menusByHookSuffix = [];

    public function addByHook($hook, $menu)
    {
        $this->menusByHookSuffix[$hook] = $menu;
        return $this;
    }

    public function findByHook($hook)
    {
        return $this->menusByHookSuffix[$hook] ?? null;
    }

    public function add(Menu $menu)
    {
        $this->menus[] = $menu;
        return $this;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->menus);
    }

    public function count()
    {
        return count($this->menus);
    }
}