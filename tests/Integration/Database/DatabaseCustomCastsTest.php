<?php

namespace WpStarter\Tests\Integration\Database;

use WpStarter\Database\Eloquent\Casts\AsArrayObject;
use WpStarter\Database\Eloquent\Casts\AsCollection;
use WpStarter\Database\Eloquent\Casts\AsStringable;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\Schema;
use WpStarter\Support\Str;

class DatabaseCustomCastsTest extends DatabaseTestCase
{
    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        Schema::create('test_eloquent_model_with_custom_casts', function (Blueprint $table) {
            $table->increments('id');
            $table->text('array_object');
            $table->text('collection');
            $table->string('stringable');
            $table->timestamps();
        });
    }

    public function test_custom_casting()
    {
        $model = new TestEloquentModelWithCustomCasts;

        $model->array_object = ['name' => 'Taylor'];
        $model->collection = ws_collect(['name' => 'Taylor']);
        $model->stringable = Str::of('Taylor');

        $model->save();

        $model = $model->fresh();

        $this->assertEquals(['name' => 'Taylor'], $model->array_object->toArray());
        $this->assertEquals(['name' => 'Taylor'], $model->collection->toArray());
        $this->assertEquals('Taylor', (string) $model->stringable);

        $model->array_object['age'] = 34;
        $model->array_object['meta']['title'] = 'Developer';

        $model->save();

        $model = $model->fresh();

        $this->assertEquals([
            'name' => 'Taylor',
            'age' => 34,
            'meta' => ['title' => 'Developer'],
        ], $model->array_object->toArray());
    }
}

class TestEloquentModelWithCustomCasts extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'array_object' => AsArrayObject::class,
        'collection' => AsCollection::class,
        'stringable' => AsStringable::class,
    ];
}
