<?php

namespace Database;

use WpStarter\Database\Connection;
use WpStarter\Database\Query\Grammars\MariaDbGrammar;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseMariaDbQueryGrammarTest extends TestCase
{
    public function testToRawSql()
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('escape')->with('foo', false)->andReturn("'foo'");
        $grammar = new MariaDbGrammar($connection);

        $query = $grammar->substituteBindingsIntoRawSql(
            'select * from "users" where \'Hello\\\'World?\' IS NOT NULL AND "email" = ?',
            ['foo'],
        );

        $this->assertSame('select * from "users" where \'Hello\\\'World?\' IS NOT NULL AND "email" = \'foo\'', $query);
    }
}
