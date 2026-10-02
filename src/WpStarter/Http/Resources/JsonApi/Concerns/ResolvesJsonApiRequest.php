<?php

namespace WpStarter\Http\Resources\JsonApi\Concerns;

use WpStarter\Http\Request;
use WpStarter\Http\Resources\JsonApi\JsonApiRequest;

trait ResolvesJsonApiRequest
{
    /**
     * Resolve a JSON API request instance from the given HTTP request.
     *
     * @return \WpStarter\Http\Resources\JsonApi\JsonApiRequest
     */
    protected function resolveJsonApiRequestFrom(Request $request)
    {
        return $request instanceof JsonApiRequest
            ? $request
            : JsonApiRequest::createFrom($request);
    }
}
