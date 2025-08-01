<?php

namespace Modules\News\Controllers;

use Modules\Base\Abstracts\BaseApiController;
use Modules\News\Services\NewsService;
use Modules\News\Resources\NewsResource;
use Modules\News\Resources\NewsCollection;
use Modules\News\Requests\StoreNewsRequest;
use Modules\News\Requests\UpdateNewsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsController extends BaseApiController
{
    protected NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    protected function getService(): NewsService
    {
        return $this->newsService;
    }

    protected function getResource(): string
    {
        return NewsResource::class;
    }

    protected function getResourceCollection(): string
    {
        return NewsCollection::class;
    }

    protected function getStoreRequest(): string
    {
        return StoreNewsRequest::class;
    }

    protected function getUpdateRequest(): string
    {
        return UpdateNewsRequest::class;
    }

    protected function getAllowedFilters(): array
    {
        return ['category', 'tag', 'search', 'status', 'is_featured'];
    }

    /**
     * Get published news for public API
     */
    public function published(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only($this->getAllowedFilters());
        
        $news = $this->newsService->getPublishedNews($perPage, $filters);
        
        return $this->successResponse(
            new NewsCollection($news),
            'Published news retrieved successfully'
        );
    }

    /**
     * Get featured news
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);
        $news = $this->newsService->getFeaturedNews($limit);
        
        return $this->successResponse(
            NewsResource::collection($news),
            'Featured news retrieved successfully'
        );
    }

    /**
     * Get news by slug
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $news = $this->newsService->getNewsBySlug($slug);
        
        if (!$news) {
            return $this->errorResponse('News not found', 404);
        }
        
        return $this->successResponse(
            new NewsResource($news),
            'News retrieved successfully'
        );
    }

    /**
     * Get related news
     */
    public function related(string $id, Request $request): JsonResponse
    {
        $news = $this->newsService->getById($id);
        
        if (!$news) {
            return $this->errorResponse('News not found', 404);
        }
        
        $limit = $request->get('limit', 5);
        $relatedNews = $this->newsService->getRelatedNews($id, $news->category_id, $limit);
        
        return $this->successResponse(
            NewsResource::collection($relatedNews),
            'Related news retrieved successfully'
        );
    }

    /**
     * Get popular news
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $news = $this->newsService->getPopularNews($limit);
        
        return $this->successResponse(
            NewsResource::collection($news),
            'Popular news retrieved successfully'
        );
    }

    /**
     * Get news archive by month
     */
    public function archive(int $year, int $month): JsonResponse
    {
        if ($month < 1 || $month > 12) {
            return $this->errorResponse('Invalid month', 400);
        }
        
        $news = $this->newsService->getArchiveByMonth($year, $month);
        
        return $this->successResponse(
            NewsResource::collection($news),
            "News archive for {$year}-{$month} retrieved successfully"
        );
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(string $id): JsonResponse
    {
        try {
            $news = $this->newsService->toggleFeatured($id);
            
            return $this->successResponse(
                new NewsResource($news),
                'News featured status updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update featured status: ' . $e->getMessage(), 500);
        }
    }
}
