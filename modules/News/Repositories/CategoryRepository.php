<?php

namespace Modules\News\Repositories;

use Modules\Base\Abstracts\BaseRepository;
use Modules\News\Models\Category;
use Modules\Base\Enums\StatusEnum;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getActiveCategories()
    {
        return $this->model->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getRootCategories()
    {
        return $this->model->active()
            ->rootCategories()
            ->with(['children' => function ($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getCategoryWithNews($slug, $perPage = 15)
    {
        $category = $this->model->active()
            ->where('slug', $slug)
            ->first();

        if ($category) {
            $category->load(['news' => function ($q) use ($perPage) {
                $q->published()
                  ->with(['category'])
                  ->orderBy('published_at', 'desc')
                  ->paginate($perPage);
            }]);
        }

        return $category;
    }

    public function getCategoriesWithNewsCount()
    {
        return $this->model->active()
            ->withCount(['news' => function ($q) {
                $q->where('status', StatusEnum::ACTIVE);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
