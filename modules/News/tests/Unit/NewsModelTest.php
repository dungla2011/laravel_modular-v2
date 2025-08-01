<?php

namespace Modules\News\tests\Unit;

use Tests\TestCase;
use Modules\News\Models\News;
use Modules\News\Models\Category;
use Modules\News\Models\Comment;
use Modules\Base\Enums\StatusEnum;

class NewsModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clean up existing data
        News::truncate();
        Category::truncate();
        Comment::truncate();
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        News::truncate();
        Category::truncate();
        Comment::truncate();
        
        parent::tearDown();
    }

    public function test_news_model_attributes()
    {
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'status' => StatusEnum::ACTIVE,
        ]);

        $news = News::create([
            'title' => 'Test News',
            'slug' => 'test-news',
            'content' => 'This is test content for the news article.',
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now(),
            'tags' => ['technology', 'programming'],
            'is_featured' => true,
            'view_count' => 10,
        ]);

        $this->assertEquals('Test News', $news->title);
        $this->assertEquals('test-news', $news->slug);
        $this->assertEquals(StatusEnum::ACTIVE, $news->status);
        $this->assertTrue($news->is_featured);
        $this->assertEquals(10, $news->view_count);
        $this->assertEquals(['technology', 'programming'], $news->tags);
    }

    public function test_news_category_relationship()
    {
        $category = Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
            'status' => StatusEnum::ACTIVE,
        ]);

        $news = News::create([
            'title' => 'Tech News',
            'slug' => 'tech-news',
            'content' => 'Content',
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
        ]);

        $this->assertInstanceOf(Category::class, $news->category);
        $this->assertEquals('Technology', $news->category->name);
    }

    public function test_news_comments_relationship()
    {
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'status' => StatusEnum::ACTIVE,
        ]);

        $news = News::create([
            'title' => 'News with Comments',
            'slug' => 'news-with-comments',
            'content' => 'Content',
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
        ]);

        $comment = Comment::create([
            'news_id' => $news->_id,
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'content' => 'Great article!',
            'status' => StatusEnum::ACTIVE,
        ]);

        $this->assertEquals(1, $news->comments->count());
        $this->assertEquals('Great article!', $news->comments->first()->content);
    }

    public function test_published_scope()
    {
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'status' => StatusEnum::ACTIVE,
        ]);

        // Published news
        News::create([
            'title' => 'Published News',
            'slug' => 'published-news',
            'content' => 'Content',
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now()->subDay(),
        ]);

        // Draft news
        News::create([
            'title' => 'Draft News',
            'slug' => 'draft-news',
            'content' => 'Content',
            'category_id' => $category->_id,
            'status' => StatusEnum::INACTIVE,
        ]);

        // Future news
        News::create([
            'title' => 'Future News',
            'slug' => 'future-news',
            'content' => 'Content',
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
            'published_at' => now()->addDay(),
        ]);

        $publishedNews = News::published()->get();
        
        $this->assertEquals(1, $publishedNews->count());
        $this->assertEquals('Published News', $publishedNews->first()->title);
    }

    public function test_featured_scope()
    {
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'status' => StatusEnum::ACTIVE,
        ]);

        News::create([
            'title' => 'Featured News',
            'slug' => 'featured-news',
            'content' => 'Content',
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
            'is_featured' => true,
        ]);

        News::create([
            'title' => 'Regular News',
            'slug' => 'regular-news',
            'content' => 'Content',
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
            'is_featured' => false,
        ]);

        $featuredNews = News::featured()->get();
        
        $this->assertEquals(1, $featuredNews->count());
        $this->assertEquals('Featured News', $featuredNews->first()->title);
    }

    public function test_excerpt_attribute()
    {
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'status' => StatusEnum::ACTIVE,
        ]);

        $longContent = str_repeat('This is a long content. ', 50);

        $news = News::create([
            'title' => 'News with Long Content',
            'slug' => 'news-with-long-content',
            'content' => $longContent,
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
        ]);

        $this->assertNotNull($news->excerpt);
        $this->assertTrue(strlen($news->excerpt) <= 203); // 200 + '...'
    }

    public function test_reading_time_attribute()
    {
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'status' => StatusEnum::ACTIVE,
        ]);

        $content = str_repeat('word ', 300); // ~300 words

        $news = News::create([
            'title' => 'News with Content',
            'slug' => 'news-with-content',
            'content' => $content,
            'category_id' => $category->_id,
            'status' => StatusEnum::ACTIVE,
        ]);

        $this->assertStringContainsString('min read', $news->reading_time);
    }
}
