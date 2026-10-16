<?php

namespace WpStarter\Wordpress\Admin\Routing;

class Response extends \WpStarter\Http\Response
{
    protected $contentResolver;
    public function __construct($content = '', $status = 200, array $headers = [])
    {
        if($content instanceof \Closure){
            $this->contentResolver=$content;
            $content='';
        }
        parent::__construct($content, $status, $headers);
    }

    public function getContent(): string|false
    {
        if($this->contentResolver instanceof \Closure){
            return ($this->contentResolver)($this);
        }
        return parent::getContent();
    }
}
