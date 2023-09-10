<?php

namespace WpStarter\Wordpress\Admin\View;

use WpStarter\Support\Traits\Macroable;

class Action
{
    use Macroable;
    protected $text;
    protected $link;
    protected $desc;
    public function __construct($text,$link,$desc='')
    {
        $this->text=$text;
        $this->link=$link;
        $this->desc=$desc;
    }
    public function getLink(){
        return $this->link;
    }
    public function getText(){
        return $this->text;
    }
    public function getDesc(){
        return $this->desc;
    }
}
