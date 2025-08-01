<?php

namespace Modules\Base\Traits;

trait HasSlug
{
    /**
     * Boot the trait
     */
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = $model->generateSlug($model->title);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && empty($model->slug)) {
                $model->slug = $model->generateSlug($model->title);
            }
        });
    }

    /**
     * Generate slug from title
     */
    public function generateSlug(string $title): string
    {
        $slug = $this->slugify($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Convert string to slug
     */
    protected function slugify(string $text): string
    {
        // Convert Vietnamese characters
        $text = $this->removeVietnameseAccents($text);
        
        // Convert to lowercase and replace spaces with hyphens
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\-]/', '', str_replace(' ', '-', $text));
        $text = preg_replace('/-+/', '-', $text);
        
        return trim($text, '-');
    }

    /**
     * Remove Vietnamese accents
     */
    protected function removeVietnameseAccents(string $str): string
    {
        $accents = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ'
        ];

        $replacements = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd'
        ];

        return str_replace($accents, $replacements, $str);
    }

    /**
     * Check if slug exists
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::where('slug', $slug);
        
        if ($this->exists) {
            $query->where('_id', '!=', $this->_id);
        }
        
        return $query->exists();
    }

    /**
     * Find by slug
     */
    public static function findBySlug(string $slug)
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
