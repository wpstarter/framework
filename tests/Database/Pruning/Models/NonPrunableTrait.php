<?php

declare(strict_types=1);

namespace WpStarter\Tests\Database\Pruning\Models;

use WpStarter\Database\Eloquent\Prunable;

trait NonPrunableTrait
{
    use Prunable;
}
