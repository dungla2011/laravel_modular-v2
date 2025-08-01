<?php

namespace Modules\News\Repositories;

use Modules\Base\Abstracts\BaseRepository;
use Modules\News\Models\News;
use Modules\Base\Enums\StatusEnum;

class NewsRepository extends BaseRepository
{
    public function __construct(News $model)
    {
        parent::__construct($model);
    }

    public function getPublishedNews($perPage = 15, $filters = [])
    {
        $query = $this->model->published()
            ->with(['category', 'comments' => function ($q) {
                $q->approved()->topLevel();
            }])
            ->orderBy('published_at', 'desc');

        // Apply filters
        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        if (!empty($filters['tag'])) {
            $query->byTag($filters['tag']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('content', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function getFeaturedNews($limit = 5)
    {
        return $this->model->published()
            ->featured()
            ->with(['category'])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRelatedNews($newsId, $categoryId, $limit = 5)
    {
        return $this->model->published()
            ->where('_id', '!=', $newsId)
            ->byCategory($categoryId)
            ->with(['category'])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function incrementViewCount($id)
    {
        return $this->model->where('_id', $id)
            ->increment('view_count');
    }

    public function getPopularNews($limit = 10)
    {
        return $this->model->published()
            ->with(['category'])
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getNewsBySlug($slug)
    {
        return $this->model->published()
            ->where('slug', $slug)
            ->with(['category', 'comments' => function ($q) {
                $q->approved()->with(['replies' => function ($r) {
                    $r->approved();
                }]);
            }])
            ->first();
    }

    public function getArchiveByMonth($year, $month)
    {
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return $this->model->published()
            ->whereBetween('published_at', [$startDate, $endDate])
            ->with(['category'])
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
