<?php

declare(strict_types=1);

use WpStarter\Log\LogManager;

use function PHPStan\Testing\assertType;

$logManager = ws_resolve(LogManager::class);

$logManager->extend('emergency', function (): void {
    assertType('WpStarter\Log\LogManager', $this);
});
