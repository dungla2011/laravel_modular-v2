# News Module

## Overview
Complete news management system with categories, comments, and SEO features.

## Features
- ✅ **News Management**: CRUD operations with rich content support
- ✅ **Category System**: Hierarchical categories with SEO
- ✅ **Comment System**: Nested comments with moderation
- ✅ **SEO Optimization**: Meta tags, slugs, and structured data
- ✅ **Featured Content**: Featured news and popular articles
- ✅ **Advanced Filtering**: Search, categories, tags, archives
- ✅ **View Tracking**: Article view counts and popularity
- ✅ **Status Management**: Draft, published, scheduled posts

## Models

### News
- `title`, `slug`, `content`, `excerpt`
- `featured_image`, `meta_title`, `meta_description`, `meta_keywords`
- `category_id`, `tags[]`, `author_id`
- `status`, `published_at`, `view_count`, `is_featured`

### Category
- `name`, `slug`, `description`, `image`
- `parent_id` (hierarchical), `sort_order`, `status`
- `meta_title`, `meta_description`

### Comment
- `news_id`, `parent_id` (nested comments)
- `author_name`, `author_email`, `author_website`
- `content`, `status`, `ip_address`

## API Endpoints

### Public Endpoints
```
GET /api/v1/news/published              # Published news with pagination
GET /api/v1/news/featured               # Featured news
GET /api/v1/news/popular                # Popular news by view count
GET /api/v1/news/slug/{slug}            # Get news by slug
GET /api/v1/news/{id}/related           # Related news
GET /api/v1/news/archive/{year}/{month} # News archive

GET /api/v1/categories/active           # Active categories
GET /api/v1/categories/tree             # Category tree with children
GET /api/v1/categories/with-news-count  # Categories with news count
GET /api/v1/categories/slug/{slug}      # Category with news
```

### Admin Endpoints (Auth Required)
```
GET    /api/v1/admin/news               # List all news
POST   /api/v1/admin/news               # Create news
GET    /api/v1/admin/news/{id}          # Show news
PUT    /api/v1/admin/news/{id}          # Update news
DELETE /api/v1/admin/news/{id}          # Delete news
POST   /api/v1/admin/news/{id}/toggle-featured # Toggle featured
DELETE /api/v1/admin/news/bulk          # Bulk delete
POST   /api/v1/admin/news/bulk/toggle-status # Bulk status change

GET    /api/v1/admin/categories         # List categories
POST   /api/v1/admin/categories         # Create category
GET    /api/v1/admin/categories/{id}    # Show category
PUT    /api/v1/admin/categories/{id}    # Update category
DELETE /api/v1/admin/categories/{id}    # Delete category
```

## Usage Examples

### Create News
```php
$news = News::create([
    'title' => 'Breaking News',
    'content' => 'News content here...',
    'category_id' => $categoryId,
    'tags' => ['technology', 'ai'],
    'status' => StatusEnum::ACTIVE,
    'is_featured' => true,
]);
```

### Get Published News
```php
$news = $newsService->getPublishedNews(15, [
    'category' => 'technology',
    'search' => 'AI',
    'tag' => 'machine-learning'
]);
```

### Create Category Tree
```php
$parent = Category::create([
    'name' => 'Technology',
    'slug' => 'technology',
    'status' => StatusEnum::ACTIVE,
]);

$child = Category::create([
    'name' => 'AI',
    'slug' => 'ai',
    'parent_id' => $parent->_id,
    'status' => StatusEnum::ACTIVE,
]);
```

## Testing
- **Unit Tests**: Model relationships, scopes, attributes
- **Feature Tests**: API endpoints, authentication, data validation
- **Coverage**: All CRUD operations and business logic

## Dependencies
- `mongodb/laravel-mongodb`: MongoDB integration
- `Base Module`: Core abstracts and enums
- Laravel 11.x framework features
