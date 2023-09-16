<?php

namespace WpStarter\Wordpress\Setting;

use WpStarter\Support\Arr;

class Repository implements \ArrayAccess
{
    protected $data;
    protected $changes=[];
    protected $optionKey;

    public function __construct($optionKey)
    {
        $this->optionKey = $optionKey;
        $this->reload();
        if (!is_array($this->data)) {
            $this->data = [];
        }
    }

    function get($key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            Arr::set($this->data, $key, $value);
            Arr::set($this->changes, $key, $value);
        }
        return $this;
    }

    function has($key)
    {
        return Arr::has($this->data, $key);
    }

    function forget($key)
    {
        Arr::forget($this->data, $key);
        return $this;
    }

    function reload()
    {
        $this->data = get_option($this->optionKey);
        return $this;
    }
    function resetChanges(){
        $this->changes=[];
        return $this;
    }

    function save($autoload = false)
    {
        if(!$this->changes){
            return true;//no changes
        }
        $data=$this->data;
        foreach ($this->changes as $key=>$value){
            Arr::set($data,$key,$value);
        }
        $this->resetChanges();
        return update_option($this->optionKey, $data, $autoload);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __unset($name)
    {
        return $this->forget($name);
    }

    /**
     * @param $offset
     * @return bool
     *
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
}
