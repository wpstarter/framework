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
    const VERSION = '1.9.10';
    /**
     * Indicates if the application has been early bootstrapped before.
     *
     * @var bool
     */
    protected $hasBeenEarlyBootstrapped = false;

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

    /**
     * Run the given array of bootstrap classes.
     *
     * @param  string[]  $bootstrappers
     * @return void
     */
    public function earlyBootstrapWith(array $bootstrappers)
    {
        $this->hasBeenEarlyBootstrapped = true;
        foreach ($bootstrappers as $bootstrapper) {
            $this['events']->dispatch('bootstrapping: '.$bootstrapper, [$this]);

            $this->make($bootstrapper)->bootstrap($this);

            $this['events']->dispatch('bootstrapped: '.$bootstrapper, [$this]);
        }
    }
    /**
     * Determine if the application has been early bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenEarlyBootstrapped()
    {
        return $this->hasBeenEarlyBootstrapped;
    }
}
