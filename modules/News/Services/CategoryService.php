<?php

namespace Modules\News\Services;

use Modules\Base\Abstracts\BaseService;
use Modules\News\Repositories\CategoryRepository;
use Modules\News\Models\Category;
use Illuminate\Support\Str;

class CategoryService extends BaseService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        parent::__construct($categoryRepository);
    }

    public function create(array $data): Category
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        // Generate meta title if not provided
        if (empty($data['meta_title'])) {
            $data['meta_title'] = $data['name'];
        }

        return $this->categoryRepository->create($data);
    }

    public function update($id, array $data): Category
    {
        $category = $this->categoryRepository->find($id);

        // Update slug if name changed
        if (isset($data['name']) && $data['name'] !== $category->name) {
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
            }
        }

        // Update meta title if name changed and meta_title is empty
        if (isset($data['name']) && empty($data['meta_title'])) {
            $data['meta_title'] = $data['name'];
        }

        return $this->categoryRepository->update($id, $data);
    }

    public function getActiveCategories()
    {
        return $this->categoryRepository->getActiveCategories();
    }

    public function getRootCategories()
    {
        return $this->categoryRepository->getRootCategories();
    }

    public function getCategoryWithNews($slug, $perPage = 15)
    {
        return $this->categoryRepository->getCategoryWithNews($slug, $perPage);
    }

    public function getCategoriesWithNewsCount()
    {
        return $this->categoryRepository->getCategoriesWithNewsCount();
    }

    protected function generateUniqueSlug($name, $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function slugExists($slug, $excludeId = null): bool
    {
        $query = Category::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('_id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
