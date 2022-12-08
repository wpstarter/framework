<?php

namespace WpStarter\Wordpress\Http\Response\Concerns;

use WpStarter\Wordpress\View\Component;

trait HasComponents
{
    protected $components = [];
    /**
     * @var bool
     */
    protected $componentsBooted = false;
    /**
     * @var bool
     */
    protected $componentMounted = false;

    function bootComponents()
    {
        if (!$this->componentsBooted) {
            foreach ($this->components as $component) {
                if ($component instanceof Component) {
                    $component->setResponse($this);
                    if(method_exists($component,'boot')) {
                        ws_app()->call([$component, 'boot']);
                    }
                } elseif ($component instanceof \Closure) {
                    $component();
                }
            }
            $this->componentsBooted = true;
        }
    }

    function mountComponents()
    {
        if (!$this->componentMounted) {
            foreach ($this->components as $component) {
                if ($component instanceof Component) {
                    if(method_exists($component,'mount')) {
                        ws_app()->call([$component, 'mount']);
                    }
                } elseif ($component instanceof \Closure) {
                    $component();
                }
            }
            $this->componentMounted = true;
        }
    }

    function getComponents()
    {
        return $this->components;
    }

    /**
     * @param array $components
     * @return $this
     */
    function setComponents(array $components)
    {
        $this->components = $components;
        return $this;
    }
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->components[$offset]);
    }
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->components[$offset] ?? null;
    }
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->components[] = $value;
        } else {
            $this->components[$offset] = $value;
        }
    }
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->components[$offset]);
    }
}