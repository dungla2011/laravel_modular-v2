<?php

namespace Modules\Base\Enums;

enum StatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case DELETED = 'deleted';

    /**
     * Get all values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for status.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE    => 'Hoạt động',
            self::INACTIVE  => 'Không hoạt động',
            self::PENDING   => 'Chờ duyệt',
            self::DRAFT     => 'Bản nháp',
            self::PUBLISHED => 'Đã xuất bản',
            self::DELETED   => 'Đã xóa',
        };
    }

    /**
     * Get color for status.
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE    => 'success',
            self::INACTIVE  => 'secondary',
            self::PENDING   => 'warning',
            self::DRAFT     => 'info',
            self::PUBLISHED => 'primary',
            self::DELETED   => 'danger',
        };
    }
}
