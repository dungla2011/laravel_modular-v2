<?php

namespace Modules\Base\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Get all records
     */
    public function all(): Collection;

    /**
     * Find record by ID
     */
    public function find(string $id): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findOrFail(string $id): Model;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update record
     */
    public function update(string $id, array $data): Model;

    /**
     * Delete record
     */
    public function delete(string $id): bool;

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find records by criteria
     */
    public function findBy(array $criteria): Collection;

    /**
     * Find one record by criteria
     */
    public function findOneBy(array $criteria): ?Model;

    /**
     * Count records
     */
    public function count(): int;

    /**
     * Check if record exists
     */
    public function exists(string $id): bool;
}
