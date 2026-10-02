<?php

namespace WpStarter\Tests\Pagination;

use WpStarter\Http\Resources\Json\JsonResource;
use WpStarter\Pagination\CursorPaginator;
use WpStarter\Tests\Pagination\Fixtures\Models\CursorResourceTestModel;
use PHPUnit\Framework\TestCase;

class CursorResourceTest extends TestCase
{
    public function testItCanTransformToExplicitResource()
    {
        $paginator = new CursorResourceTestPaginator([
            new CursorResourceTestModel(),
        ], 1);

        $resource = $paginator->toResourceCollection(CursorResourceTestResource::class);

        $this->assertInstanceOf(JsonResource::class, $resource);
    }

    public function testItThrowsExceptionWhenResourceCannotBeFound()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Failed to find resource class for model [WpStarter\Tests\Pagination\Fixtures\Models\CursorResourceTestModel].');

        $paginator = new CursorResourceTestPaginator([
            new CursorResourceTestModel(),
        ], 1);

        $paginator->toResourceCollection();
    }

    public function testItCanGuessResourceWhenNotProvided()
    {
        $paginator = new CursorResourceTestPaginator([
            new CursorResourceTestModel(),
        ], 1);

        class_alias(CursorResourceTestResource::class, 'WpStarter\Tests\Pagination\Fixtures\Http\Resources\CursorResourceTestModelResource');

        $resource = $paginator->toResourceCollection();

        $this->assertInstanceOf(JsonResource::class, $resource);
    }
}

class CursorResourceTestResource extends JsonResource
{
    //
}

class CursorResourceTestPaginator extends CursorPaginator
{
    //
}
