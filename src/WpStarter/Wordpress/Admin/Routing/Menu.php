<?php

namespace WpStarter\Wordpress\Admin\Routing;


use WpStarter\Routing\Matching\MethodValidator;
use WpStarter\Routing\Route;
use WpStarter\Support\Str;
use WpStarter\Wordpress\Admin\Routing\Matching\ScreenIdValidator;
use WpStarter\Wordpress\Admin\View\Layout;

class Menu extends Route
{
    public static $validators;

    public $hookSuffix;
    protected $layout;
    protected $response;

    public function initialize(){
        if(!$this->getAction('title')){
            $this->title(Str::headline($this->uri()));
        }
        if(!$this->getAction('page_title')){
            $this->pageTitle($this->getAction('title'));
        }
        if(!$this->getAction('capability')){
            $this->capability('read');
        }
        $this->layout()->title($this->getAction('page_title'));
        $this->layout()->setNoticeManager($this->container['wp.admin.notice']);
    }

    /**
     * Get the route validators for the instance.
     *
     * @return array
     */
    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }

        // To match the route, we will use a chain of responsibility pattern with the
        // validator implementations. We will spin through each one making sure it
        // passes and then we will know if the route as a whole matches request.
        return static::$validators = [
            new MethodValidator,
            new ScreenIdValidator,
        ];
    }

    /**
     * Create new group for children menu
     * @param $callback
     * @return $this
     */
    public function group($callback){
        $this->router->group([
            'parent'=>$this->uri(),
            'name'=>$this->getName(),
            'capability'=>$this->getAction('capability')
        ],$callback);
        return $this;
    }

    public function setResponse($response){
        $this->response=$response;
        return $this;
    }
    public function getResponse(){
        return $this->response;
    }

    function layout(){
        if(!$this->layout){
            $this->layout=new Layout();
        }
        return $this->layout;
    }

    /**
     * Hide the menu
     * @return $this
     */
    public function hide($hide=true){
        if($hide) {
            $this->action['hide'] = $this->action['parent']??null;
            $this->parent('ws-admin-hidden-menu');
            global $_parent_pages;
            $_parent_pages['ws-admin-hidden-menu']=false;
        }else{
            if($this->getAction('parent') ==='ws-admin-hidden-menu'){
                $this->parent($this->getAction('hide'));
                unset($this->action['hide']);
            }
        }
        return $this;
    }
    /**
     * @param mixed $page_title
     * @return Menu
     */
    public function pageTitle($page_title)
    {
        $this->action['page_title']=$page_title;
        return $this;
    }

    /**
     * @param mixed $menu_title
     * @return Menu
     */
    public function title($menu_title)
    {
        $this->action['title']=$menu_title;
        return $this;
    }

    /**
     * @param mixed $capability
     * @return Menu
     */
    public function capability($capability)
    {
        $this->action['capability']=$capability;
        return $this;
    }


    /**
     * @param mixed|string $callback
     * @return Menu
     */
    public function callback($callback)
    {
        return $this->uses($callback);
    }

    /**
     * @param mixed|string $icon_url The URL to the icon to be used for this menu.
     * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme. This should begin with 'data:image/svg+xml;base64,'.
     * Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'.
     * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
     *
     * @return Menu
     */
    public function iconUrl($icon_url)
    {
        $this->action['icon']=$icon_url;
        return $this;
    }

    /**
     * @param mixed|null $position
     * @return Menu
     */
    public function position($position)
    {
        $this->action['position']=$position;
        return $this;
    }

    /**
     * @param mixed $parent
     * @return Menu
     */
    public function parent($parent)
    {
        $this->action['parent']=$parent;
        return $this;
    }




}
