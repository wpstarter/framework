<?php

namespace WpStarter\Wordpress;
use WpStarter\Http\Response as BaseResponse;
abstract class Response extends BaseResponse
{
    protected $headerIsAlreadySent=false;
    protected $titleParts=[];
    protected $documentTitle;
    protected $titlePart;
    protected $componentBooted=false;
    protected $componentMounted=false;
    public function __construct(?string $content = '', int $status = 200, array $headers = [])
    {
        parent::__construct($content, $status, $headers);
    }
    public function bootComponent(){

    }
    public function mountComponent(){

    }


    public function getDocumentTitle($title){
        if($this->documentTitle){
            if($this->documentTitle instanceof \Closure){
                $title=call_user_func($this->documentTitle,$title);
            }else{
                $title=$this->documentTitle;
            }
        }
        return $title;
    }
    public function withDocumentTitle($title){
        $this->documentTitle=$title;
        return $this;
    }

    /**
     * Set title in title parts
     * @param $title
     * @return $this
     */
    function withTitle($title){
        $this->titlePart=$title;
        return $this;
    }

    /**
     * Set title for given part
     * @param $part
     * @param $title
     * @return $this
     */
    function withTitlePart($part,$title){
        $this->titleParts[$part]=$title;
        return $this;
    }

    /**
     * Set whole titleParts
     * @param $partOrResolver
     * @return $this
     */
    function withTitleParts($partOrResolver){
        $this->titleParts=$partOrResolver;
        return $this;
    }
    function getTitleParts($parts){
        if($this->titleParts instanceof \Closure){
            $parts=call_user_func($this->titleParts,$parts);
            if(!is_array($parts)){
                throw new \RuntimeException("Title parts should be array");
            }
        }elseif(is_array($this->titleParts)){
            $parts=array_merge($parts,$this->titleParts);
        }
        if($this->titlePart){
            $titlePart=$parts['title']??'';
            if($this->titlePart instanceof \Closure){
                $titlePart=call_user_func($this->titlePart,$titlePart);
            }else{
                $titlePart=$this->titlePart;
            }
            $parts['title']=$titlePart;
        }
        return $parts;
    }

    public function sendHeaders(){
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