<?php

namespace WpStarter\Wordpress\Setting;

use WpStarter\Support\Arr;

class Repository implements \ArrayAccess
{
    protected $data;
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
                Arr::set($this->data, $k, $v);
            }
        } else {
            Arr::set($this->data, $key, $value);
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

    function save($autoload = false)
    {
        return update_option($this->optionKey, $this->data, $autoload);
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

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
}