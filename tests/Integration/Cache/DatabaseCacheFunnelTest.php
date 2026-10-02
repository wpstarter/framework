<?php

namespace WpStarter\Tests\Integration\Cache;

use WpStarter\Contracts\Cache\Repository;
use WpStarter\Foundation\Testing\LazilyRefreshDatabase;
use WpStarter\Support\Facades\Cache;
use Orchestra\Testbench\Attributes\WithMigration;

#[WithMigration('cache')]
class DatabaseCacheFunnelTest extends CacheFunnelTestCase
{
    use LazilyRefreshDatabase;

    protected function cache(): Repository
    {
        return Cache::store('database');
    }
}
