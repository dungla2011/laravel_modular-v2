<?php

namespace Modules\Base\Abstracts;

use Modules\Base\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(string $id): bool
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function findBy(array $criteria): Collection
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }
        
        return $query->get();
    }

    public function findOneBy(array $criteria): ?Model
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }
        
        return $query->first();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function exists(string $id): bool
    {
        return $this->model->where('_id', $id)->exists();
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, 'like', "%{$value}%");
                }
            }
        }
        
        return $query;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting($query, string $sortField = 'created_at', string $sortDirection = 'desc')
    {
        return $query->orderBy($sortField, $sortDirection);
    }
}
