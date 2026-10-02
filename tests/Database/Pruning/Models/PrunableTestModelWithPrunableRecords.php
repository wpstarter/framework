<?php

declare(strict_types=1);

namespace WpStarter\Tests\Database\Pruning\Models;

use WpStarter\Database\Eloquent\MassPrunable;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Events\ModelsPruned;

class PrunableTestModelWithPrunableRecords extends Model
{
    use MassPrunable;

    protected $table = 'prunables';
    protected $connection = 'default';

    public function pruneAll()
    {
        event(new ModelsPruned(static::class, 10));
        event(new ModelsPruned(static::class, 20));

        return 20;
    }

    public function prunable()
    {
        return static::where('value', '>=', 3);
    }
}
