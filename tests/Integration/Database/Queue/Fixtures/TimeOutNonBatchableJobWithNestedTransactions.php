<?php

namespace WpStarter\Tests\Integration\Database\Queue\Fixtures;

use WpStarter\Bus\Queueable;
use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Queue\InteractsWithQueue;
use WpStarter\Support\Facades\DB;

class TimeOutNonBatchableJobWithNestedTransactions implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public int $tries = 1;
    public int $timeout = 2;

    public function handle(): void
    {
        DB::transaction(function () {
            DB::transaction(fn () => sleep(20));
        });
    }
}
