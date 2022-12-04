<?php

namespace WpStarter\Wordpress\Admin\View;

class Layout
{
    protected $title;
    protected $subTitle;
    protected $action;
    protected $messages=[];

    public function __construct()
    {

    }


    public function withSuccess($message){
        $notice=new Notice($message,'success');
        $this->addMessage($notice);
        return $notice;
    }
    public function withInfo($message){
        $notice=new Notice($message,'info');
        $this->addMessage($notice);
        return $notice;
    }
    public function withWarning($message){
        $notice=new Notice($message,'warning');
        $this->addMessage($notice);
        return $notice;
    }
    public function withError($message){
        $notice=new Notice($message,'error');
        $this->addMessage($notice);
        return $notice;
    }
    public function addMessage($message){
        $this->messages[]=$message;
        ws_session()->put('admin.layout.messages',$this->messages);
        return $this;
    }
    public function getMessages(){
        $messages=$this->messages;
        if(!is_array($messages)){
            $messages=[];
        }
        $messages=array_filter($messages,function($m){
            return $m instanceof Notice;
        });
        return $messages;
    }
    public function loadMessages(){
        $this->messages=ws_session('admin.layout.messages',[]);
    }
    public function clearMessages(){
        $this->messages[]='';
        ws_session()->forget('admin.layout.messages');
        return $this;
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