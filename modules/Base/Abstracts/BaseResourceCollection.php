<?php

namespace Modules\Base\Abstracts;

use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'data'  => $this->collection,
            'meta'  => $this->getMeta(),
            'links' => $this->getLinks($request),
        ];
    }

    /**
     * Get meta information for the collection.
     */
    protected function getMeta(): array
    {
        return [
            'total'          => $this->resource->total(),
            'count'          => $this->resource->count(),
            'per_page'       => $this->resource->perPage(),
            'current_page'   => $this->resource->currentPage(),
            'total_pages'    => $this->resource->lastPage(),
            'has_more_pages' => $this->resource->hasMorePages(),
        ];
    }

    /**
     * Get pagination links.
     */
    protected function getLinks($request): array
    {
        return [
            'first' => $this->resource->url(1),
            'last'  => $this->resource->url($this->resource->lastPage()),
            'prev'  => $this->resource->previousPageUrl(),
            'next'  => $this->resource->nextPageUrl(),
            'self'  => $request->fullUrl(),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with($request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->toISOString(),
                'timezone'  => config('app.timezone'),
            ],
        ];
    }
}
