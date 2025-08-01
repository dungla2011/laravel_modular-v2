<?php

namespace Modules\News\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->when($request->route()->getName() === 'news.show', $this->content),
            'featured_image' => $this->featured_image,
            'meta' => $this->when($request->route()->getName() === 'news.show', [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
            ]),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => $this->tags ?? [],
            'author_id' => $this->author_id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'published_at_human' => $this->published_at?->diffForHumans(),
            'view_count' => $this->view_count ?? 0,
            'is_featured' => $this->is_featured ?? false,
            'sort_order' => $this->sort_order ?? 0,
            'reading_time' => $this->reading_time,
            'comments_count' => $this->whenLoaded('comments', function () {
                return $this->comments->count();
            }),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
