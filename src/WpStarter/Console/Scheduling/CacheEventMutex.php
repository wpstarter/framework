<?php

namespace WpStarter\Console\Scheduling;

use WpStarter\Cache\DynamoDbStore;
use WpStarter\Contracts\Cache\Factory as Cache;
use WpStarter\Contracts\Cache\LockProvider;

class CacheEventMutex implements EventMutex, CacheAware
{
    /**
     * The cache repository implementation.
     *
     * @var \WpStarter\Contracts\Cache\Factory
     */
    public $cache;

    /**
     * The cache store that should be used.
     *
     * @var string|null
     */
    public $store;

    /**
     * Create a new overlapping strategy.
     *
     * @param  \WpStarter\Contracts\Cache\Factory  $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Attempt to obtain an event mutex for the given event.
     *
     * @param  \WpStarter\Console\Scheduling\Event  $event
     * @return bool
     */
    public function create(Event $event)
    {
        if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
            return $this->cache->store($this->store)->getStore()
                ->lock($event->mutexName(), $event->expiresAt * 60)
                ->acquire();
        }

        return $this->cache->store($this->store)->add(
            $event->mutexName(), true, $event->expiresAt * 60
        );
    }

    /**
     * Determine if an event mutex exists for the given event.
     *
     * @param  \WpStarter\Console\Scheduling\Event  $event
     * @return bool
     */
    public function exists(Event $event)
    {
        if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
            return ! $this->cache->store($this->store)->getStore()
                ->lock($event->mutexName(), $event->expiresAt * 60)
                ->get(fn () => true);
        }

        return $this->cache->store($this->store)->has($event->mutexName());
    }

    /**
     * Clear the event mutex for the given event.
     *
     * @param  \WpStarter\Console\Scheduling\Event  $event
     * @return void
     */
    public function forget(Event $event)
    {
        if ($this->shouldUseLocks($this->cache->store($this->store)->getStore())) {
            $this->cache->store($this->store)->getStore()
                ->lock($event->mutexName(), $event->expiresAt * 60)
                ->forceRelease();

            return;
        }

        $this->cache->store($this->store)->forget($event->mutexName());
    }

    /**
     * Determine if the given store should use locks for cache event mutexes.
     *
     * @param  \WpStarter\Contracts\Cache\Store  $store
     * @return bool
     */
    protected function shouldUseLocks($store)
    {
        return $store instanceof LockProvider && ! $store instanceof DynamoDbStore;
    }

    /**
     * Specify the cache store that should be used.
     *
     * @param  string  $store
     * @return $this
     */
    public function useStore($store)
    {
        $this->store = $store;

        return $this;
    }
}
