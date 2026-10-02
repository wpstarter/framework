<?php

namespace WpStarter\Tests\Integration\Cache;

use WpStarter\Contracts\Cache\Repository;
use WpStarter\Foundation\Testing\Concerns\InteractsWithRedis;
use WpStarter\Support\Facades\Cache;

class RedisCacheFunnelTest extends CacheFunnelTestCase
{
    use InteractsWithRedis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpRedis();

        Cache::purge('redis');

        $this->releaseFunnelLocks();
    }

    protected function cache(): Repository
    {
        return Cache::store('redis');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tearDownRedis();
    }
}
