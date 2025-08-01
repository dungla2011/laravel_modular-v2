<?php

namespace Modules\Base\Traits;

trait HasStatus
{
    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
            'draft' => 'Draft',
            'published' => 'Published',
        ];
    }

    /**
     * Scope active records
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope inactive records
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope pending records
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if record is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if record is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Set status to active
     */
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Set status to inactive
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }
}
