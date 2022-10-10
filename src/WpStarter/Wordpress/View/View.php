<?php

namespace WpStarter\Wordpress\View;

class View extends Component
{
    protected $view;
    protected $preparerers=[];
    public function __construct($view)
    {
        $this->view=$view;
    }

    public function prepare($prepare){
       $this->preparerers[]=$prepare;
       return $this;
    }

    public function render()
    {
        foreach ($this->preparerers as $preparerer){
            $this->data=$preparerer($this->data);
        }
        return ws_view($this->view,$this->data)->render();
    }
}