<?php

namespace WpStarter\Tests\Integration\Database\EloquentModelCustomEventsTest;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\Event;
use WpStarter\Support\Facades\Schema;
use WpStarter\Tests\Integration\Database\DatabaseTestCase;

class EloquentModelCustomEventsTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::listen(CustomEvent::class, function () {
            $_SERVER['fired_event'] = true;
        });
    }

    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        Schema::create('test_model1', function (Blueprint $table) {
            $table->increments('id');
        });
    }

    public function testFlushListenersClearsCustomEvents()
    {
        $_SERVER['fired_event'] = false;

        TestModel1::flushEventListeners();

        TestModel1::create();

        $this->assertFalse($_SERVER['fired_event']);
    }

    public function testCustomEventListenersAreFired()
    {
        $_SERVER['fired_event'] = false;

        TestModel1::create();

        $this->assertTrue($_SERVER['fired_event']);
    }
}

class TestModel1 extends Model
{
    public $dispatchesEvents = ['created' => CustomEvent::class];
    public $table = 'test_model1';
    public $timestamps = false;
    protected $guarded = [];
}

class CustomEvent
{
    //
}
