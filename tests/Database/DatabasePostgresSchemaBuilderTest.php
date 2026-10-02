<?php

namespace WpStarter\Tests\Database;

use WpStarter\Database\Connection;
use WpStarter\Database\Query\Processors\PostgresProcessor;
use WpStarter\Database\Schema\Grammars\PostgresGrammar;
use WpStarter\Database\Schema\PostgresBuilder;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabasePostgresSchemaBuilderTest extends TestCase
{
    public function testHasTable()
    {
        $connection = m::mock(Connection::class);
        $grammar = m::mock(PostgresGrammar::class);
        $connection->shouldReceive('getSchemaGrammar')->andReturn($grammar);
        $builder = new PostgresBuilder($connection);
        $grammar->shouldReceive('compileTableExists')->twice()->andReturn('sql');
        $connection->shouldReceive('getTablePrefix')->twice()->andReturn('prefix_');
        $connection->shouldReceive('scalar')->twice()->with('sql')->andReturn(1);

        $this->assertTrue($builder->hasTable('table'));
        $this->assertTrue($builder->hasTable('public.table'));
    }

    public function testGetColumnListing()
    {
        $connection = m::mock(Connection::class);
        $grammar = m::mock(PostgresGrammar::class);
        $processor = m::mock(PostgresProcessor::class);
        $connection->shouldReceive('getSchemaGrammar')->andReturn($grammar);
        $connection->shouldReceive('getPostProcessor')->andReturn($processor);
        $grammar->shouldReceive('compileColumns')->with(null, 'prefix_table')->once()->andReturn('sql');
        $processor->shouldReceive('processColumns')->once()->andReturn([['name' => 'column']]);
        $builder = new PostgresBuilder($connection);
        $connection->shouldReceive('getTablePrefix')->once()->andReturn('prefix_');
        $connection->shouldReceive('selectFromWriteConnection')->once()->with('sql')->andReturn([['name' => 'column']]);

        $this->assertEquals(['column'], $builder->getColumnListing('table'));
    }
}
