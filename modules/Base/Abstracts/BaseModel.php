<?php

namespace Modules\Base\Abstracts;

use MongoDB\Laravel\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb';

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return '_id';
    }

    /**
     * Scope a query to only include active records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest($query, string $column = 'created_at')
    {
        return $query->orderBy($column, 'desc');
    }

    /**
     * Scope a query to order by oldest.
     */
    public function scopeOldest($query, string $column = 'created_at')
    {
        return $query->orderBy($column, 'asc');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });

        static::updating(function ($model) {
            $model->updated_at = now();
        });
    }
}
