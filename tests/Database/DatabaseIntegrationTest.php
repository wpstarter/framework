<?php

namespace WpStarter\Tests\Database;

use WpStarter\Database\Capsule\Manager as DB;
use WpStarter\Database\Events\QueryExecuted;
use WpStarter\Events\Dispatcher;
use PHPUnit\Framework\TestCase;

class DatabaseIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        $db = new DB;
        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $db->setAsGlobal();
        $db->setEventDispatcher(new Dispatcher);
    }

    public function testQueryExecutedToRawSql(): void
    {
        $connection = DB::connection();

        $connection->listen(function (QueryExecuted $query) use (&$queryExecuted): void {
            $queryExecuted = $query;
        });

        $connection->select('select ?', [true]);

        $this->assertInstanceOf(QueryExecuted::class, $queryExecuted);
        $this->assertSame('select ?', $queryExecuted->sql);
        $this->assertSame([true], $queryExecuted->bindings);
        $this->assertSame('select 1', $queryExecuted->toRawSql());
    }
}
