<?php

namespace WpStarter\Support;

class DefaultProviders
{
    /**
     * The current providers.
     *
     * @var array
     */
    protected $providers;

    /**
     * Create a new default provider collection.
     */
    public function __construct(?array $providers = null)
    {
        $this->providers = $providers ?: [
            \WpStarter\Auth\AuthServiceProvider::class,
            \WpStarter\Broadcasting\BroadcastServiceProvider::class,
            \WpStarter\Bus\BusServiceProvider::class,
            \WpStarter\Cache\CacheServiceProvider::class,
            \WpStarter\Foundation\Providers\ConsoleSupportServiceProvider::class,
            \WpStarter\Concurrency\ConcurrencyServiceProvider::class,
            \WpStarter\Cookie\CookieServiceProvider::class,
            \WpStarter\Database\DatabaseServiceProvider::class,
            \WpStarter\Encryption\EncryptionServiceProvider::class,
            \WpStarter\Filesystem\FilesystemServiceProvider::class,
            \WpStarter\Foundation\Providers\FoundationServiceProvider::class,
            \WpStarter\Hashing\HashServiceProvider::class,
            \WpStarter\Mail\MailServiceProvider::class,
            \WpStarter\Notifications\NotificationServiceProvider::class,
            \WpStarter\Pagination\PaginationServiceProvider::class,
            \WpStarter\Auth\Passwords\PasswordResetServiceProvider::class,
            \WpStarter\Pipeline\PipelineServiceProvider::class,
            \WpStarter\Queue\QueueServiceProvider::class,
            \WpStarter\Redis\RedisServiceProvider::class,
            \WpStarter\Session\SessionServiceProvider::class,
            \WpStarter\Translation\TranslationServiceProvider::class,
            \WpStarter\Validation\ValidationServiceProvider::class,
            \WpStarter\View\ViewServiceProvider::class,
        ];
    }

    /**
     * Merge the given providers into the provider collection.
     *
     * @param  array  $providers
     * @return static
     */
    public function merge(array $providers)
    {
        $this->providers = array_merge($this->providers, $providers);

        return new static($this->providers);
    }

    /**
     * Replace the given providers with other providers.
     *
     * @param  array  $replacements
     * @return static
     */
    public function replace(array $replacements)
    {
        $current = new Collection($this->providers);

        foreach ($replacements as $from => $to) {
            $key = $current->search($from);

            $current = is_int($key) ? $current->replace([$key => $to]) : $current;
        }

        return new static($current->values()->toArray());
    }

    /**
     * Disable the given providers.
     *
     * @param  array  $providers
     * @return static
     */
    public function except(array $providers)
    {
        return new static((new Collection($this->providers))
            ->reject(fn ($p) => in_array($p, $providers))
            ->values()
            ->toArray());
    }

    /**
     * Convert the provider collection to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->providers;
    }
}
