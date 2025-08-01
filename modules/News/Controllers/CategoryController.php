<?php

namespace Modules\News\Controllers;

use Modules\Base\Abstracts\BaseApiController;
use Modules\News\Services\CategoryService;
use Modules\News\Resources\CategoryResource;
use Modules\News\Requests\StoreCategoryRequest;
use Modules\News\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseApiController
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    protected function getService(): CategoryService
    {
        return $this->categoryService;
    }

    protected function getResource(): string
    {
        return CategoryResource::class;
    }

    protected function getResourceCollection(): string
    {
        return CategoryResource::class;
    }

    protected function getStoreRequest(): string
    {
        return StoreCategoryRequest::class;
    }

    protected function getUpdateRequest(): string
    {
        return UpdateCategoryRequest::class;
    }

    protected function getAllowedFilters(): array
    {
        return ['status', 'parent_id'];
    }

    /**
     * Get active categories for public API
     */
    public function active(): JsonResponse
    {
        $categories = $this->categoryService->getActiveCategories();
        
        return $this->successResponse(
            CategoryResource::collection($categories),
            'Active categories retrieved successfully'
        );
    }

    /**
     * Get root categories with children
     */
    public function tree(): JsonResponse
    {
        $categories = $this->categoryService->getRootCategories();
        
        return $this->successResponse(
            CategoryResource::collection($categories),
            'Category tree retrieved successfully'
        );
    }

    /**
     * Get category by slug with news
     */
    public function showBySlug(string $slug, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $category = $this->categoryService->getCategoryWithNews($slug, $perPage);
        
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        
        return $this->successResponse(
            new CategoryResource($category),
            'Category with news retrieved successfully'
        );
    }

    /**
     * Get categories with news count
     */
    public function withNewsCount(): JsonResponse
    {
        $categories = $this->categoryService->getCategoriesWithNewsCount();
        
        return $this->successResponse(
            CategoryResource::collection($categories),
            'Categories with news count retrieved successfully'
        );
    }
}
