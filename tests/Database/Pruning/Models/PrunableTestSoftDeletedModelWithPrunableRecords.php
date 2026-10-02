<?php

declare(strict_types=1);

namespace WpStarter\Tests\Database\Pruning\Models;

use WpStarter\Database\Eloquent\MassPrunable;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Eloquent\SoftDeletes;

class PrunableTestSoftDeletedModelWithPrunableRecords extends Model
{
    use MassPrunable, SoftDeletes;

    protected $table = 'prunables';
    protected $connection = 'default';

    public function prunable()
    {
        return static::where('value', '>=', 3);
    }
}
