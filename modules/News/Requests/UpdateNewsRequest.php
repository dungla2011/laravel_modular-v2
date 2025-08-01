<?php

namespace Modules\News\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Base\Enums\StatusEnum;
use Illuminate\Validation\Rule;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add proper authorization logic here
    }

    public function rules(): array
    {
        $newsId = $this->route('news') ?? $this->route('id');
        
        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $newsId . ',_id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'sometimes|required|string',
            'featured_image' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'category_id' => 'sometimes|required|string|exists:news_categories,_id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'author_id' => 'nullable|string',
            'status' => ['sometimes', 'required', Rule::enum(StatusEnum::class)],
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'News title is required.',
            'title.max' => 'News title cannot exceed 255 characters.',
            'content.required' => 'News content is required.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category does not exist.',
            'slug.unique' => 'This slug is already taken.',
            'featured_image.max' => 'Featured image path cannot exceed 255 characters.',
            'meta_title.max' => 'Meta title cannot exceed 255 characters.',
            'meta_description.max' => 'Meta description cannot exceed 500 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_map('trim', explode(',', $this->tags))
            ]);
        }

        if ($this->has('is_featured')) {
            $this->merge([
                'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}
