<?php

namespace WpStarter\Tests\Integration\Database;

use WpStarter\Database\Eloquent\Collection as EloquentCollection;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\Schema;
use WpStarter\Tests\Integration\Database\Fixtures\User;

class EloquentCollectionFreshTest extends DatabaseTestCase
{
    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
        });
    }

    public function testEloquentCollectionFresh()
    {
        User::insert([
            ['email' => 'laravel@framework.com'],
            ['email' => 'laravel@laravel.com'],
        ]);

        $collection = User::all();

        $collection->first()->delete();

        $freshCollection = $collection->fresh();

        $this->assertCount(1, $freshCollection);
        $this->assertInstanceOf(EloquentCollection::class, $freshCollection);
    }
}
