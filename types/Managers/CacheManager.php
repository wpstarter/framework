<?php

declare(strict_types=1);

use WpStarter\Cache\CacheManager;

use function PHPStan\Testing\assertType;

$cacheManager = ws_resolve(CacheManager::class);

$cacheManager->extend('redis', function (): void {
    assertType('WpStarter\Cache\CacheManager', $this);
});
