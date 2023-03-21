<?php

namespace WpStarter\Tests\Integration\Database;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\Schema;

class EloquentModelWithoutEventsTest extends DatabaseTestCase
{
    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        Schema::create('auto_filled_models', function (Blueprint $table) {
            $table->increments('id');
            $table->text('project')->nullable();
        });
    }

    public function testWithoutEventsRegistersBootedListenersForLater()
    {
        $model = AutoFilledModel::withoutEvents(function () {
            return AutoFilledModel::create();
        });

        $this->assertNull($model->project);

        $model->save();

        $this->assertSame('Laravel', $model->project);
    }
}

class AutoFilledModel extends Model
{
    public $table = 'auto_filled_models';
    public $timestamps = false;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->project = 'Laravel';
        });
    }
}
