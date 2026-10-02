<?php

declare(strict_types=1);

use WpStarter\Redis\RedisManager;

use function PHPStan\Testing\assertType;

$redisManager = resolve(RedisManager::class);

$redisManager->extend('custom', function (): void {
    assertType('WpStarter\Redis\RedisManager', $this);
});
