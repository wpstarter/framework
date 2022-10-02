<?php

namespace WpStarter\Wordpress;
use WpStarter\Http\Response as BaseResponse;
class Response extends BaseResponse
{
    protected $headerIsAlreadySent=false;
    public function __construct(?string $content = '', int $status = 200, array $headers = [])
    {
        parent::__construct($content, $status, $headers);
    }
    public function mountComponent(){

    }

    public function sendHeaders()
    {
        if(!$this->headerIsAlreadySent){
            $this->headerIsAlreadySent=true;
            return parent::sendHeaders();
        }
    }

    /**
     * Sends content for the current web response.
     *
     * @return $this
     */
    public function sendContent()
    {
        echo $this->getContent();

        return $this;
    }
}