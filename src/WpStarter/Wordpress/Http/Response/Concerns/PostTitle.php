<?php

namespace WpStarter\Wordpress\Http\Response\Concerns;

trait PostTitle
{
    protected $postTitle;
    function withPostTitle($title){
        $this->postTitle=$title;
        return $this;
    }
    function getPostTitle($title=null){
        if($this->postTitle){
            return static::unwrapIfClosure($this->postTitle,$title);
        }
        return $title;
    }
}