<?php

namespace WpStarter\Tests\Database;

use WpStarter\Database\ClassMorphViolationException;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Eloquent\Relations\Relation;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentStrictMorphsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Relation::requireMorphMap();
    }

    public function testStrictModeThrowsAnExceptionOnClassMap()
    {
        $this->expectException(ClassMorphViolationException::class);

        $model = TestModel::make();

        $model->getMorphClass();
    }

    public function testStrictModeDoesNotThrowExceptionWhenMorphMap()
    {
        $model = TestModel::make();

        Relation::morphMap([
            'test' => TestModel::class,
        ]);

        $morphName = $model->getMorphClass();
        $this->assertEquals('test', $morphName);
    }

    public function testMapsCanBeEnforcedInOneMethod()
    {
        $model = TestModel::make();

        Relation::requireMorphMap(false);

        Relation::enforceMorphMap([
            'test' => TestModel::class,
        ]);

        $morphName = $model->getMorphClass();
        $this->assertEquals('test', $morphName);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Relation::morphMap([], false);
        Relation::requireMorphMap(false);
    }
}

class TestModel extends Model
{
}
