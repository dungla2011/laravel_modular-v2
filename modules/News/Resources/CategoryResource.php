<?php

namespace Modules\News\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order ?? 0,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
            ],
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'news_count' => $this->when(isset($this->news_count), $this->news_count),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
