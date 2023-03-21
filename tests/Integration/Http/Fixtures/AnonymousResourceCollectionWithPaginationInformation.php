<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Http\Resources\Json\AnonymousResourceCollection;

class AnonymousResourceCollectionWithPaginationInformation extends AnonymousResourceCollection
{
    public function paginationInformation($request)
    {
        $paginated = $this->resource->toArray();

        return [
            'current_page' => $paginated['current_page'],
            'per_page' => $paginated['per_page'],
            'total' => $paginated['total'],
            'total_page' => $paginated['last_page'],
        ];
    }
}
