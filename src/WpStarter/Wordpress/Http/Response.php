<?php

namespace WpStarter\Wordpress\Http;

use WpStarter\Http\Response as BaseResponse;
use WpStarter\Wordpress\Http\Response\Concerns\HasComponents;

abstract class Response extends BaseResponse implements \ArrayAccess
{
    use HasComponents;

    protected $headerIsAlreadySent = false;
    protected $titleParts = [];
    protected $documentTitle;

    public function getDocumentTitle($title)
    {
        if ($this->documentTitle) {
            return static::unwrapIfClosure($this->documentTitle, $title);
        }
        return $title;
    }

    public function withDocumentTitle($title)
    {
        $this->documentTitle = $title;
        return $this;
    }

    /**
     * Set title in title parts
     * @param $title
     * @return $this
     */
    function withTitle($title, $part = 'title')
    {
        $this->titleParts[$part] = $title;
        return $this;
    }

    /**
     * Set whole titleParts
     * @param $partOrResolver
     * @return $this
     */
    function setTitleParts($partOrResolver)
    {
        $this->titleParts = $partOrResolver;
        return $this;
    }

    function getTitleParts($parts)
    {
        $newParts = [];
        if ($this->titleParts) {
            $newParts = static::unwrapIfClosure($this->titleParts, $parts);
        }
        if (!isset($newParts['title']) && isset($this->postTitle)) {
            $newParts['title'] = $this->postTitle;
        }
        foreach ($newParts as $part => $value) {
            $oldValue = $parts[$part] ?? null;
            $parts[$part] = static::unwrapIfClosure($value, $oldValue);
        }

        return $parts;
    }

    public function sendHeaders()
    {
        if (!$this->headerIsAlreadySent) {
            $this->headerIsAlreadySent = true;
            return parent::sendHeaders();
        }
        return $this;
    }

    public static function unwrapIfClosure($value, ...$args)
    {
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