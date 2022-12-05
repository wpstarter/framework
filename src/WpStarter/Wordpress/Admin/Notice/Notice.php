<?php

namespace WpStarter\Wordpress\Admin\Notice;

class Notice
{
    protected $message;
    protected $type;
    protected $dismissible;
    public function __construct($message='',$type='info',$dismissible=false)
    {
        $this->message=$message;
        $this->type=$type;
        $this->dismissible=$dismissible;
    }

    function error($message){
        return $this->message($message,'error');
    }
    function warning($message){
        return $this->message($message,'warning');
    }
    function success($message){
        return $this->message($message,'success');
    }
    function info($message){
        return $this->message($message,'info');
    }

    function dismissible($flag=true){
        $this->dismissible=$flag;
        return $this;
    }
    function message($message,$type){
        $this->message=$message;
        $this->type=$type;
        return $this;
    }
    function render(){
        $classes=['notice','notice-'.$this->type];
        if($this->dismissible){
            $classes[]='is-dismissible';
        }
        return sprintf('<div class="%s"><p>%s</p></div>',join(' ',$classes),$this->message);
    }
    public static function make($message,$type='info',$dismissible=false){
        return new static($message,$type,$dismissible);
    }
}