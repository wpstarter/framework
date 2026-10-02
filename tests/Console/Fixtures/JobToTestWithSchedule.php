<?php

declare(strict_types=1);

namespace WpStarter\Tests\Console\Fixtures;

use WpStarter\Contracts\Queue\ShouldQueue;

final class JobToTestWithSchedule implements ShouldQueue
{
}
