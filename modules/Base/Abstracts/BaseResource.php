<?php

namespace Modules\Base\Abstracts;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
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

    /**
     * Customize the response for a request.
     */
    public function withResponse($request, $response): void
    {
        $response->header('Content-Type', 'application/json');
    }
}
