<?php

namespace WpStarter\Wordpress\Setting;

use WpStarter\Support\Arr;

class Repository implements \ArrayAccess
{
    protected $data;
    protected $isChanged=false;
    protected $optionKey;

    public function __construct($optionKey)
    {
        $this->optionKey = $optionKey;
        $this->reload();

    }

    function get($key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    function set($key, $value = null): static
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            $this->isChanged = true;
            if($value === null){
                Arr::forget($this->data,$key);
            }else {
                Arr::set($this->data, $key, $value);
            }
        }
        return $this;
    }

    function has($key): bool{
        return Arr::has($this->data,$key);

    }

    function forget(...$keys): static{
        $keys=is_array($keys[0]??null)?$keys[0]:$keys;
        foreach ($keys as $key) {
            $this->set($key, null);
        }
        return $this;
    }

    function reload(): static{
        $this->data = get_option($this->optionKey);
        if (!is_array($this->data)) {
            $this->data = [];
        }
        $this->resetChanges();
        return $this;
    }
    function resetChanges(): static{
        $this->isChanged=false;
        return $this;
    }
    function isDirty(): bool{
        return $this->isChanged;
    }

    function save($autoload = false)
    {
        if(!$this->isDirty()){
            return true;//no changes
        }
        $updated=update_option($this->optionKey, $this->data, $autoload);
        if($updated){
            $this->resetChanges();
        }
        return $updated;
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
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetUnset($offset): void
    {
        $this->forget($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }
}
