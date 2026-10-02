<?php

namespace WpStarter\Tests\Integration\Database;

use WpStarter\Support\Facades\DB;

class ConnectionThreadsCountTest extends DatabaseTestCase
{
    public function testGetThreadsCount()
    {
        $count = DB::connection()->threadCount();

        if ($this->driver === 'sqlite') {
            $this->assertNull($count, 'SQLite does not support connection count');
        } else {
            $this->assertGreaterThanOrEqual(1, $count);
        }
    }
}
