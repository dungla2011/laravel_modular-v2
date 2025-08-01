<?php

namespace Modules\News\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Base\Enums\StatusEnum;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add proper authorization logic here
    }

    public function rules(): array
    {
        $categoryId = $this->route('category') ?? $this->route('id');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news_categories,slug,' . $categoryId . ',_id',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|string|max:255',
            'parent_id' => 'nullable|string|exists:news_categories,_id',
            'sort_order' => 'nullable|integer|min:0',
            'status' => ['sometimes', 'required', Rule::enum(StatusEnum::class)],
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'slug.unique' => 'This slug is already taken.',
            'parent_id.exists' => 'Selected parent category does not exist.',
            'sort_order.min' => 'Sort order must be at least 0.',
        ];
    }
}
