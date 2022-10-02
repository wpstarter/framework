<?php

namespace WpStarter\Auth;

use InvalidArgumentException;

trait CreatesUserProviders
{
    /**
     * The registered custom provider creators.
     *
     * @var array
     */
    protected $customProviderCreators = [];

    /**
     * Create the user provider implementation for the driver.
     *
     * @param  string|null  $provider
     * @return \WpStarter\Contracts\Auth\UserProvider|null
     *
     * @throws \InvalidArgumentException
     */
    public function createUserProvider($provider = null)
    {
        if (is_null($config = $this->getProviderConfiguration($provider))) {
            return;
        }

        if (isset($this->customProviderCreators[$driver = ($config['driver'] ?? null)])) {
            return call_user_func(
                $this->customProviderCreators[$driver], $this->app, $config
            );
        }

        switch ($driver) {
            case 'wp':
                return $this->createWpProvider($config);
            default:
                throw new InvalidArgumentException(
                    "Authentication user provider [{$driver}] is not defined."
                );
        }
    }

    /**
     * Get the user provider configuration.
     *
     * @param  string|null  $provider
     * @return array|null
     */
    protected function getProviderConfiguration($provider)
    {
        if ($provider = $provider ?: $this->getDefaultUserProvider()) {
            return $this->app['config']['auth.providers.'.$provider];
        }
    }


    /**
     * Create an instance of the Eloquent user provider.
     *
     * @param  array  $config
     * @return \WpStarter\Auth\WpUserProvider
     */
    protected function createWpProvider($config)
    {
        return new WpUserProvider($config['model']);
    }

    /**
     * Get the default user provider name.
     *
     * @return string
     */
    public function getDefaultUserProvider()
    {
        return $this->app['config']['auth.defaults.provider'];
    }
}
