<?php

namespace WpStarter\Wordpress\Response\Concerns;

trait PostTitle
{
    protected $postTitle;
    function withPostTitle($title){
        $this->postTitle=$title;
        return $this;
    }
    function getPostTitle($title=null){
        if($this->postTitle){
            if($this->postTitle instanceof \Closure){
                $title = call_user_func($this->postTitle,$title);
            }else {
                $title = $this->postTitle;
            }
        }
        return $title;
    }
}