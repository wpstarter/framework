<?php

declare(strict_types=1);

namespace WpStarter\Tests\Database\Pruning\Models;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Eloquent\Prunable;

abstract class AbstractPrunableModel extends Model
{
    use Prunable;
}
