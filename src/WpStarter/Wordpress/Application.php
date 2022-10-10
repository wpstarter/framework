<?php

namespace WpStarter\Wordpress;

use WpStarter\Wordpress\Routing\RoutingServiceProvider;

class Application extends \WpStarter\Foundation\Application
{
    /**
     * The WpStarter framework version.
     *
     * @var string
     */
    const VERSION = '1.0.11';
    protected function registerBaseServiceProviders()
    {
        parent::registerBaseServiceProviders();
        $this->register(new RoutingServiceProvider($this));
    }

    function bootstrapWith(array $bootstrappers)
    {
        $this->hasBeenBootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this->bootstrapOne($bootstrapper);
        }
    }
    protected function bootstrapOne($bootstrapper){
        $this['events']->dispatch('bootstrapping: '.$bootstrapper, [$this]);
        $this->make($bootstrapper)->bootstrap($this);
        $this['events']->dispatch('bootstrapped: '.$bootstrapper, [$this]);
    }
}