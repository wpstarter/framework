<?php

namespace WpStarter\Wordpress\Admin\Notice;

use WpStarter\Session\SessionManager;

class NoticeManager
{
    protected $notices=[];
    protected $session;
    protected $loaded;
    public function __construct(SessionManager $sessionManager)
    {
        $this->session=$sessionManager;
    }

    public function notify($message,$type){
        $notice=new Notice($message,$type);
        $this->addNotice($notice);
        return $notice;
    }
    public function success($message){
        return $this->notify($message,'success');
    }
    public function info($message){
        return $this->notify($message,'info');
    }
    public function warning($message){
        return $this->notify($message,'warning');
    }
    public function error($message){
        return $this->notify($message,'error');
    }
    public function addNotice(Notice $notice){
        $this->notices[]=$notice;
        $this->session->put('admin.notices',$this->notices);
        return $this;
    }
    public function all(){
        $notices=$this->session->get('admin.notices',[]);
        if(!is_array($notices)){
            $notices=[];
        }
        return array_filter($notices,function($notice){
            return $notice instanceof Notice;
        });
    }
    public function clear(){
        $this->notices[]='';
        $this->session->forget('admin.notices');
        return $this;
    }
}