<?php

namespace WpStarter\Tests\Integration\Database\EloquentLazyEagerLoadingTest;

use DB;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\Schema;
use WpStarter\Tests\Integration\Database\DatabaseTestCase;

class EloquentLazyEagerLoadingTest extends DatabaseTestCase
{
    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        Schema::create('one', function (Blueprint $table) {
            $table->increments('id');
        });

        Schema::create('two', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('one_id');
        });

        Schema::create('three', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('one_id');
        });
    }

    public function testItBasic()
    {
        $one = Model1::create();
        $one->twos()->create();
        $one->threes()->create();

        $model = Model1::find($one->id);

        $this->assertTrue($model->relationLoaded('twos'));
        $this->assertFalse($model->relationLoaded('threes'));

        DB::enableQueryLog();

        $model->load('threes');

        $this->assertCount(1, DB::getQueryLog());

        $this->assertTrue($model->relationLoaded('threes'));
    }
}

class Model1 extends Model
{
    public $table = 'one';
    public $timestamps = false;
    protected $guarded = [];
    protected $with = ['twos'];

    public function twos()
    {
        return $this->hasMany(Model2::class, 'one_id');
    }

    public function threes()
    {
        return $this->hasMany(Model3::class, 'one_id');
    }
}

class Model2 extends Model
{
    public $table = 'two';
    public $timestamps = false;
    protected $guarded = [];

    public function one()
    {
        return $this->belongsTo(Model1::class, 'one_id');
    }
}

class Model3 extends Model
{
    public $table = 'three';
    public $timestamps = false;
    protected $guarded = [];

    public function one()
    {
        return $this->belongsTo(Model1::class, 'one_id');
    }
}
