<?php

namespace WpStarter\Wordpress\Admin\Routing;


class MenuCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var Menu[]
     */
    protected $menus = [];
    /**
     * @var Menu[]
     */
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

    /**
     * @param $slug
     * @return Menu|null
     */
    public function find($slug){
        return $this->menus[$slug] ?? null;
    }

    public function add(Menu $menu)
    {
        $slug=$menu->slug;
        if($menu->parent){
            $slug=$menu->parent.'_'.$slug;
        }
        $this->menus[$slug] = $menu;
        return $this;
    }
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->menus);
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->menus);
    }
}
