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
    const VERSION = '1.8.2';

    protected $bootstrappedList = [];

    protected function registerBaseServiceProviders()
    {
        if(!function_exists('add_filter') // Not run inside WordPress
        ){
            require_once __DIR__.'/noop.php';
        }
        parent::registerBaseServiceProviders();
        $this->register(new RoutingServiceProvider($this));
    }
    public function registerCoreContainerAliases()
    {
        parent::registerCoreContainerAliases();
        $this->alias('app',self::class);
    }

    function bootstrapWith(array $bootstrappers)
    {
        $this->hasBeenBootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this->bootstrapOne($bootstrapper);
        }
    }

    function bootstrapOne($bootstrapper)
    {
        if (!isset($this->bootstrappedList[$bootstrapper])) {
            $this->bootstrappedList[$bootstrapper] = true;
            $this['events']->dispatch('bootstrapping: ' . $bootstrapper, [$this]);
            $this->make($bootstrapper)->bootstrap($this);
            $this['events']->dispatch('bootstrapped: ' . $bootstrapper, [$this]);
        }
    }
}
