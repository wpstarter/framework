<?php

namespace WpStarter\Tests\Integration\Cache;

use WpStarter\Contracts\Cache\Repository;
use WpStarter\Support\Facades\Cache;
use Orchestra\Testbench\Attributes\WithConfig;

#[WithConfig('cache.default', 'file')]
class FileCacheFunnelTest extends CacheFunnelTestCase
{
    protected function cache(): Repository
    {
        return Cache::store('file');
    }
}
