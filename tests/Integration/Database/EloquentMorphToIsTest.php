<?php

namespace WpStarter\Tests\Integration\Database\EloquentMorphToIsTest;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Schema\Blueprint;
use WpStarter\Support\Facades\Schema;
use WpStarter\Tests\Integration\Database\DatabaseTestCase;

class EloquentMorphToIsTest extends DatabaseTestCase
{
    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('commentable_type');
            $table->integer('commentable_id');
        });

        $post = Post::create();
        (new Comment)->commentable()->associate($post)->save();
    }

    public function testParentIsNotNull()
    {
        $child = Comment::first();
        $parent = null;

        $this->assertFalse($child->commentable()->is($parent));
        $this->assertTrue($child->commentable()->isNot($parent));
    }

    public function testParentIsModel()
    {
        $child = Comment::first();
        $parent = Post::first();

        $this->assertTrue($child->commentable()->is($parent));
        $this->assertFalse($child->commentable()->isNot($parent));
    }

    public function testParentIsNotAnotherModel()
    {
        $child = Comment::first();
        $parent = new Post;
        $parent->id = 2;

        $this->assertFalse($child->commentable()->is($parent));
        $this->assertTrue($child->commentable()->isNot($parent));
    }

    public function testNullParentIsNotModel()
    {
        $child = Comment::first();
        $child->commentable()->dissociate();
        $parent = Post::first();

        $this->assertFalse($child->commentable()->is($parent));
        $this->assertTrue($child->commentable()->isNot($parent));
    }

    public function testParentIsNotModelWithAnotherTable()
    {
        $child = Comment::first();
        $parent = Post::first();
        $parent->setTable('foo');

        $this->assertFalse($child->commentable()->is($parent));
        $this->assertTrue($child->commentable()->isNot($parent));
    }

    public function testParentIsNotModelWithAnotherConnection()
    {
        $child = Comment::first();
        $parent = Post::first();
        $parent->setConnection('foo');

        $this->assertFalse($child->commentable()->is($parent));
        $this->assertTrue($child->commentable()->isNot($parent));
    }
}

class Comment extends Model
{
    public $timestamps = false;

    public function commentable()
    {
        return $this->morphTo();
    }
}

class Post extends Model
{
    //
}
