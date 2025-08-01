<?php

namespace Modules\News\tests\Feature;

use Tests\TestCase;
use Modules\News\Models\News;
use Modules\News\Models\Category;
use Modules\Base\Enums\StatusEnum;

class NewsApiTest extends TestCase
{
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clean up existing data
        News::truncate();
        Category::truncate();
        
        // Create a test category
        $this->category = Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Technology news and updates',
            'status' => StatusEnum::ACTIVE,
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        News::truncate();
        Category::truncate();
        
        parent::tearDown();
    }

    public function test_can_get_published_news()
    {
        // Create published news
        News::create([
            'title' => 'Test News',
            'slug' => 'test-news',
            'content' => 'This is test news content',
            'category_id' => $this->category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/news/published');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'title',
                                'slug',
                                'excerpt',
                                'featured_image',
                                'category',
                                'tags',
                                'status',
                                'published_at',
                                'view_count',
                                'is_featured',
                            ]
                        ],
                        'meta',
                        'links',
                    ]
                ]);
    }

    public function test_can_get_featured_news()
    {
        // Create featured news
        News::create([
            'title' => 'Featured News',
            'slug' => 'featured-news',
            'content' => 'This is featured news content',
            'category_id' => $this->category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now(),
            'is_featured' => true,
        ]);

        $response = $this->getJson('/api/v1/news/featured');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Featured news retrieved successfully'
                ]);
    }

    public function test_can_get_news_by_slug()
    {
        $news = News::create([
            'title' => 'Slug Test News',
            'slug' => 'slug-test-news',
            'content' => 'This is slug test news content',
            'category_id' => $this->category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/news/slug/slug-test-news');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'News retrieved successfully',
                    'data' => [
                        'title' => 'Slug Test News',
                        'slug' => 'slug-test-news',
                    ]
                ]);
    }

    public function test_returns_404_for_nonexistent_news_slug()
    {
        $response = $this->getJson('/api/v1/news/slug/nonexistent-slug');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'News not found'
                ]);
    }

    public function test_can_get_popular_news()
    {
        // Create news with different view counts
        News::create([
            'title' => 'Popular News 1',
            'slug' => 'popular-news-1',
            'content' => 'Content 1',
            'category_id' => $this->category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now(),
            'view_count' => 100,
        ]);

        News::create([
            'title' => 'Popular News 2',
            'slug' => 'popular-news-2',
            'content' => 'Content 2',
            'category_id' => $this->category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now(),
            'view_count' => 50,
        ]);

        $response = $this->getJson('/api/v1/news/popular');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Popular news retrieved successfully'
                ]);
    }

    public function test_can_get_news_archive()
    {
        $date = now()->subMonth();
        
        News::create([
            'title' => 'Archive News',
            'slug' => 'archive-news',
            'content' => 'Archive content',
            'category_id' => $this->category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => $date,
        ]);

        $response = $this->getJson('/api/v1/news/archive/' . $date->year . '/' . $date->month);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                ]);
    }

    public function test_can_get_related_news()
    {
        $news1 = News::create([
            'title' => 'News 1',
            'slug' => 'news-1',
            'content' => 'Content 1',
            'category_id' => $this->category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now(),
        ]);

        News::create([
            'title' => 'Related News',
            'slug' => 'related-news',
            'content' => 'Related content',
            'category_id' => $this->category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/news/' . $news1->_id . '/related');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Related news retrieved successfully'
                ]);
    }
}
