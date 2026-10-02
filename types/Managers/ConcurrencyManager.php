<?php

declare(strict_types=1);

use WpStarter\Concurrency\ConcurrencyManager;

use function PHPStan\Testing\assertType;

$concurrencyManager = ws_resolve(ConcurrencyManager::class);

$concurrencyManager->extend('custom', function (): void {
    assertType('WpStarter\Concurrency\ConcurrencyManager', $this);
});
