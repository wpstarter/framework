<?php

namespace WpStarter\Wordpress\Admin\View;

use WpStarter\Wordpress\Admin\Notice\NoticeManager;

class Layout
{
    protected $title;
    protected $subTitle;
    protected $action;
    protected $messages=[];
    /**
     * @var NoticeManager
     */
    protected $notice;
    public function __construct()
    {

    }
    public function setNoticeManager($manager){
        $this->notice=$manager;
        return $this;
    }
    public function getNotices(){
        return $this->notice->all();
    }
    public function clearNotices(){
        return $this->notice->clear();
    }
    public function title($title){
        $this->title=$title;
        return $this;
    }
    public function subTitle($subtitle){
        $this->subTitle=$subtitle;
        return $this;
    }
    public function action($text,$link,$desc=''){
        $this->action=new Action($text,$link,$desc);
        return $this;
    }
    public function getTitle(){
        return $this->title;
    }
    public function getSubTitle(){
        return $this->subTitle;
    }

    /**
     * @return Action|null
     */
    public function getAction(){
        return $this->action;
    }
}