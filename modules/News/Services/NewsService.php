<?php

namespace Modules\News\Services;

use Modules\Base\Abstracts\BaseService;
use Modules\News\Repositories\NewsRepository;
use Modules\News\Models\News;
use Modules\Base\Enums\StatusEnum;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsService extends BaseService
{
    protected NewsRepository $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
        parent::__construct($newsRepository);
    }

    public function create(array $data): News
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }

        // Set published_at if status is active and no date provided
        if ($data['status'] === StatusEnum::ACTIVE && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Generate meta title if not provided
        if (empty($data['meta_title'])) {
            $data['meta_title'] = $data['title'];
        }

        // Generate excerpt if not provided
        if (empty($data['excerpt'])) {
            $data['excerpt'] = Str::limit(strip_tags($data['content']), 200);
        }

        return $this->newsRepository->create($data);
    }

    public function update($id, array $data): News
    {
        $news = $this->newsRepository->find($id);

        // Update slug if title changed
        if (isset($data['title']) && $data['title'] !== $news->title) {
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $id);
            }
        }

        // Update published_at when changing to active status
        if (isset($data['status']) && $data['status'] === StatusEnum::ACTIVE && !$news->published_at) {
            $data['published_at'] = now();
        }

        // Update meta title if title changed and meta_title is empty
        if (isset($data['title']) && empty($data['meta_title'])) {
            $data['meta_title'] = $data['title'];
        }

        return $this->newsRepository->update($id, $data);
    }

    public function getPublishedNews($perPage = 15, $filters = [])
    {
        return $this->newsRepository->getPublishedNews($perPage, $filters);
    }

    public function getFeaturedNews($limit = 5)
    {
        return $this->newsRepository->getFeaturedNews($limit);
    }

    public function getNewsBySlug($slug)
    {
        $news = $this->newsRepository->getNewsBySlug($slug);
        
        if ($news) {
            // Increment view count
            $this->newsRepository->incrementViewCount($news->_id);
        }

        return $news;
    }

    public function getRelatedNews($newsId, $categoryId, $limit = 5)
    {
        return $this->newsRepository->getRelatedNews($newsId, $categoryId, $limit);
    }

    public function getPopularNews($limit = 10)
    {
        return $this->newsRepository->getPopularNews($limit);
    }

    public function getArchiveByMonth($year, $month)
    {
        return $this->newsRepository->getArchiveByMonth($year, $month);
    }

    public function toggleFeatured($id): News
    {
        $news = $this->newsRepository->find($id);
        
        return $this->newsRepository->update($id, [
            'is_featured' => !$news->is_featured
        ]);
    }

    protected function generateUniqueSlug($title, $excludeId = null): string
    {
        $slug = Str::slug($title);
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
        $query = News::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('_id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
