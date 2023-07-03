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

    protected $menusByName=[];



    public function add(Menu $menu)
    {
        $this->addToCollection($menu);
        $this->addLookups($menu);
        return $menu;
    }

    protected function addToCollection(Menu $menu){
        $slug=$menu->slug;
        if($menu->parent){
            $slug=$menu->parent.'_'.$slug;
        }
        $this->menus[$slug] = $menu;
    }

    /**
     * @param Menu $menu
     * @return $this
     */
    public function addLookups(Menu $menu)
    {
        if($menu->hookSuffix){
            $this->menusByHookSuffix[$menu->hookSuffix] = $menu;
        }
        if($menu->name){
            $this->menusByName[$menu->name]=$menu;
        }

        return $this;
    }


    public function findByHook($hook)
    {
        return $this->menusByHookSuffix[$hook] ?? null;
    }

    public function findByName($name){
        return $this->menusByName[$name] ?? null;
    }

    public function findBySlug($slug){
        return $this->menus[$slug]??null;
    }

    /**
     * @param $slug
     * @return Menu|null
     */
    public function find($slug){
        return $this->findByName($slug) ?? $this->findBySlug($slug);
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
