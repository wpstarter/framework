<?php

namespace WpStarter\Tests\Pagination;

use WpStarter\Database\Eloquent\Collection;
use WpStarter\Pagination\AbstractCursorPaginator;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class CursorPaginatorLoadMorphTest extends TestCase
{
    public function testCollectionLoadMorphCanChainOnThePaginator()
    {
        $relations = [
            'App\\User' => 'photos',
            'App\\Company' => ['employees', 'calendars'],
        ];

        $items = m::mock(Collection::class);
        $items->shouldReceive('loadMorph')->once()->with('parentable', $relations);

        $p = (new class extends AbstractCursorPaginator
        {
            //
        })->setCollection($items);

        $this->assertSame($p, $p->loadMorph('parentable', $relations));
    }
}
