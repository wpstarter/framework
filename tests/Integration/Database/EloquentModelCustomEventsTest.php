<?php

namespace WpStarter\Tests\Integration\Database\EloquentModelCustomEventsTest;

use WpStarter\Database\Eloquent\Attributes\ObservedBy;
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

    protected function afterRefreshingDatabase()
    {
        Schema::create('test_model1', function (Blueprint $table) {
            $table->increments('id');
        });

        Schema::create('eloquent_model_stub_with_custom_event_from_traits', function (Blueprint $table) {
            $table->boolean('custom_attribute');
            $table->boolean('observer_attribute');
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

    public function testAddObservableEventFromTrait()
    {
        $model = new EloquentModelStubWithCustomEventFromTrait();

        $this->assertNull($model->custom_attribute);
        $this->assertNull($model->observer_attribute);

        $model->completeCustomAction();

        $this->assertTrue($model->custom_attribute);
        $this->assertTrue($model->observer_attribute);
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

trait CustomEventTrait
{
    public function completeCustomAction()
    {
        $this->custom_attribute = true;

        $this->fireModelEvent('customEvent');
    }

    public function initializeCustomEventTrait()
    {
        $this->addObservableEvents([
            'customEvent',
        ]);
    }
}

class CustomObserver
{
    public function customEvent(EloquentModelStubWithCustomEventFromTrait $model)
    {
        $model->observer_attribute = true;
    }
}

#[ObservedBy(CustomObserver::class)]
class EloquentModelStubWithCustomEventFromTrait extends Model
{
    use CustomEventTrait;

    public $timestamps = false;
}
