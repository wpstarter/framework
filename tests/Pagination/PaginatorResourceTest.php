<?php

namespace WpStarter\Tests\Pagination;

use WpStarter\Http\Resources\Json\JsonResource;
use WpStarter\Pagination\LengthAwarePaginator;
use WpStarter\Tests\Pagination\Fixtures\Models\PaginatorResourceTestModel;
use PHPUnit\Framework\TestCase;

class PaginatorResourceTest extends TestCase
{
    public function testItCanTransformToExplicitResource()
    {
        $paginator = new PaginatorResourceTestPaginator([
            new PaginatorResourceTestModel(),
        ], 1, 1, 1);

        $resource = $paginator->toResourceCollection(PaginatorResourceTestResource::class);

        $this->assertInstanceOf(JsonResource::class, $resource);
    }

    public function testItThrowsExceptionWhenResourceCannotBeFound()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Failed to find resource class for model [WpStarter\Tests\Pagination\Fixtures\Models\PaginatorResourceTestModel].');

        $paginator = new PaginatorResourceTestPaginator([
            new PaginatorResourceTestModel(),
        ], 1, 1, 1);

        $paginator->toResourceCollection();
    }

    public function testItCanGuessResourceWhenNotProvided()
    {
        $paginator = new PaginatorResourceTestPaginator([
            new PaginatorResourceTestModel(),
        ], 1, 1, 1);

        class_alias(PaginatorResourceTestResource::class, 'WpStarter\Tests\Pagination\Fixtures\Http\Resources\PaginatorResourceTestModelResource');

        $resource = $paginator->toResourceCollection();

        $this->assertInstanceOf(JsonResource::class, $resource);
    }
}

class PaginatorResourceTestResource extends JsonResource
{
    //
}

class PaginatorResourceTestPaginator extends LengthAwarePaginator
{
    //
}
