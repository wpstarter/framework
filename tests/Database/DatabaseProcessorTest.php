<?php

namespace WpStarter\Tests\Database;

use WpStarter\Database\Connection;
use WpStarter\Database\Query\Builder;
use WpStarter\Database\Query\Processors\Processor;
use Mockery as m;
use PDO;
use PHPUnit\Framework\TestCase;

class DatabaseProcessorTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testInsertGetIdProcessing()
    {
        $pdo = $this->createMock(ProcessorTestPDOStub::class);
        $pdo->expects($this->once())->method('lastInsertId')->with($this->equalTo('id'))->willReturn('1');
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('insert')->once()->with('sql', ['foo']);
        $connection->shouldReceive('getPdo')->once()->andReturn($pdo);
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('getConnection')->andReturn($connection);
        $processor = new Processor;
        $result = $processor->processInsertGetId($builder, 'sql', ['foo'], 'id');
        $this->assertSame(1, $result);
    }
}

class ProcessorTestPDOStub extends PDO
{
    public function __construct()
    {
        //
    }

    #[\ReturnTypeWillChange]
    public function lastInsertId($sequence = null)
    {
        //
    }
}
