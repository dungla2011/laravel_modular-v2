<?php

namespace Modules\News\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->_id,
            'news_id' => $this->news_id,
            'parent_id' => $this->parent_id,
            'author_name' => $this->author_name,
            'author_email' => $this->author_email,
            'author_website' => $this->author_website,
            'content' => $this->content,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}
