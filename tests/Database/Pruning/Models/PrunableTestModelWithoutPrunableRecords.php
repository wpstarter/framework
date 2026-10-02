<?php

declare(strict_types=1);

namespace WpStarter\Tests\Database\Pruning\Models;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Eloquent\Prunable;

class PrunableTestModelWithoutPrunableRecords extends Model
{
    use Prunable;

    public function pruneAll()
    {
        return 0;
    }
}
