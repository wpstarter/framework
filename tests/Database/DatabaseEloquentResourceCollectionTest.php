<?php

namespace WpStarter\Tests\Database;

use WpStarter\Database\Eloquent\Collection;
use WpStarter\Http\Resources\Json\AnonymousResourceCollection;
use WpStarter\Http\Resources\Json\JsonResource;
use WpStarter\Tests\Database\Fixtures\Models\EloquentResourceCollectionTestModel;
use WpStarter\Tests\Database\Fixtures\Models\EloquentResourceTestResourceModelWithUseResourceAttribute;
use WpStarter\Tests\Database\Fixtures\Models\EloquentResourceTestResourceModelWithUseResourceCollectionAttribute;
use WpStarter\Tests\Database\Fixtures\Resources\EloquentResourceCollectionTestResource;
use WpStarter\Tests\Database\Fixtures\Resources\EloquentResourceTestJsonResource;
use WpStarter\Tests\Database\Fixtures\Resources\EloquentResourceTestJsonResourceCollection;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentResourceCollectionTest extends TestCase
{
    public function testItCanTransformToExplicitResource()
    {
        $collection = new Collection([
            new EloquentResourceCollectionTestModel(),
        ]);

        $resource = $collection->toResourceCollection(EloquentResourceCollectionTestResource::class);

        $this->assertInstanceOf(JsonResource::class, $resource);
    }

    public function testItThrowsExceptionWhenResourceCannotBeFound()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Failed to find resource class for model [WpStarter\Tests\Database\Fixtures\Models\EloquentResourceCollectionTestModel].');

        $collection = new Collection([
            new EloquentResourceCollectionTestModel(),
        ]);
        $collection->toResourceCollection();
    }

    public function testItCanGuessResourceWhenNotProvided()
    {
        $collection = new Collection([
            new EloquentResourceCollectionTestModel(),
        ]);

        class_alias(EloquentResourceCollectionTestResource::class, 'WpStarter\Tests\Database\Fixtures\Http\Resources\EloquentResourceCollectionTestModelResource');

        $resource = $collection->toResourceCollection();

        $this->assertInstanceOf(JsonResource::class, $resource);
    }

    public function testItCanTransformToResourceViaUseResourceAttribute()
    {
        $collection = new Collection([
            new EloquentResourceTestResourceModelWithUseResourceCollectionAttribute(),
        ]);

        $resource = $collection->toResourceCollection();

        $this->assertInstanceOf(EloquentResourceTestJsonResourceCollection::class, $resource);
    }

    public function testItCanTransformToResourceViaUseResourceCollectionAttribute()
    {
        $collection = new Collection([
            new EloquentResourceTestResourceModelWithUseResourceAttribute(),
        ]);

        $resource = $collection->toResourceCollection();

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resource);
        $this->assertInstanceOf(EloquentResourceTestJsonResource::class, $resource[0]);
    }
}
