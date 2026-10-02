<?php

namespace WpStarter\Http\Resources\JsonApi;

use WpStarter\Container\Container;
use WpStarter\Http\JsonResponse;
use WpStarter\Http\Request;
use WpStarter\Support\Arr;

class AnonymousResourceCollection extends \WpStarter\Http\Resources\Json\AnonymousResourceCollection
{
    use Concerns\ResolvesJsonApiRequest;

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return array
     */
    #[\Override]
    public function with($request)
    {
        return array_filter([
            'included' => $this->collection
                ->map(fn ($resource) => $resource->resolveIncludedResourceObjects($request))
                ->flatten(depth: 1)
                ->uniqueStrict('_uniqueKey')
                ->map(fn ($included) => Arr::except($included, ['_uniqueKey']))
                ->values()
                ->all(),
            ...($implementation = JsonApiResource::$jsonApiInformation)
                ? ['jsonapi' => $implementation]
                : [],
        ]);
    }

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return array
     */
    #[\Override]
    public function toAttributes(Request $request)
    {
        return $this->collection
            ->map(fn ($resource) => $resource->resolveResourceData($request))
            ->all();
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \WpStarter\Http\JsonResponse  $response
     * @return void
     */
    #[\Override]
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->header('Content-Type', 'application/vnd.api+json');
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return \WpStarter\Http\JsonResponse
     */
    #[\Override]
    public function toResponse($request)
    {
        return parent::toResponse($this->resolveJsonApiRequestFrom($request));
    }

    /**
     * Resolve the HTTP request instance from container.
     *
     * @return \WpStarter\Http\Resources\JsonApi\SparseRequest
     */
    #[\Override]
    protected function resolveRequestFromContainer()
    {
        return $this->resolveJsonApiRequestFrom(Container::getInstance()->make('request'));
    }
}
