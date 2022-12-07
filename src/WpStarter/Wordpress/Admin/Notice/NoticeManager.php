<?php

namespace WpStarter\Wordpress\Admin\Notice;

class NoticeManager
{
    protected $notices=[];
    protected $store;
    protected $loaded;
    public function __construct(Store $store)
    {
        $this->store=$store;
    }
    public function withStore(Store $store){
        return new static($store);
    }

    public function notify($message,$type){
        $notice=new Message($message,$type);
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
    public function addNotice(Message $notice){
        $this->notices[]=$notice;
        $this->store->put($this->notices);
        return $this;
    }
    public function all(){
        $notices=$this->store->get();
        if(!is_array($notices)){
            $notices=[];
        }
        return array_filter($notices,function($notice){
            return $notice instanceof Message;
        });
    }
    public function clear(){
        $this->notices[]='';
        $this->store->forget();
        return $this;
    }
}