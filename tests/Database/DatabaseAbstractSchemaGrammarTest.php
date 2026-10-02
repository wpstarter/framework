<?php

namespace WpStarter\Tests\Database;

use WpStarter\Database\Connection;
use WpStarter\Database\Schema\Grammars\Grammar;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseAbstractSchemaGrammarTest extends TestCase
{
    public function testCreateDatabase()
    {
        $connection = m::mock(Connection::class);
        $grammar = new class($connection) extends Grammar {
        };

        $this->assertSame('create database "foo"', $grammar->compileCreateDatabase('foo'));
    }

    public function testDropDatabaseIfExists()
    {
        $connection = m::mock(Connection::class);
        $grammar = new class($connection) extends Grammar {
        };

        $this->assertSame('drop database if exists "foo"', $grammar->compileDropDatabaseIfExists('foo'));
    }
}
