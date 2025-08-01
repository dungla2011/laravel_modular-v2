<?php

namespace Modules\Base\Contracts;

interface ServiceInterface
{
    /**
     * Get all records
     */
    public function getAll();

    /**
     * Get record by ID
     */
    public function getById(string $id);

    /**
     * Create new record
     */
    public function create(array $data);

    /**
     * Update record
     */
    public function update(string $id, array $data);

    /**
     * Delete record
     */
    public function delete(string $id): bool;

    /**
     * Get paginated records
     */
    public function getPaginated(int $perPage = 15);
}
