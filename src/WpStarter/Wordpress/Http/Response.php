<?php

namespace WpStarter\Wordpress\Http;
use WpStarter\Http\Response as BaseResponse;
abstract class Response extends BaseResponse
{
    protected $headerIsAlreadySent=false;
    protected $titleParts=[];
    protected $documentTitle;
    protected $titlePart;
    protected $componentsBooted=false;
    protected $componentMounted=false;
    public function __construct(?string $content = '', int $status = 200, array $headers = [])
    {
        parent::__construct($content, $status, $headers);
    }
    public function bootComponents(){

    }
    public function mountComponents(){

    }


    public function getDocumentTitle($title){
        if($this->documentTitle){
            return static::unwrapIfClosure($this->documentTitle,$title);
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
        $newParts=[];
        if($this->titleParts){
            $newParts=static::unwrapIfClosure($this->titleParts,$parts);
        }
        $parts=array_merge($parts,$newParts);
        $title=$this->titlePart?:$this->postTitle??'';
        $parts['title']=static::unwrapIfClosure($title,$parts['title']??'');
        return $parts;
    }

    public function sendHeaders(){
        if(!$this->headerIsAlreadySent){
            $this->headerIsAlreadySent=true;
            return parent::sendHeaders();
        }
    }

    public static function unwrapIfClosure($value,...$args){
        return $value instanceof \Closure ? $value(...$args) : $value;
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