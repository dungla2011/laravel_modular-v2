<?php

namespace Modules\Base\Abstracts;

use Modules\Base\Contracts\RepositoryInterface;
use Modules\Base\Contracts\ServiceInterface;

abstract class BaseService implements ServiceInterface
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getById(string $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        $validatedData = $this->validateData($data, 'create');

        return $this->repository->create($validatedData);
    }

    public function update(string $id, array $data)
    {
        $validatedData = $this->validateData($data, 'update');

        return $this->repository->update($id, $validatedData);
    }

    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginated(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Validate data before processing
     * Override this method in child classes.
     */
    protected function validateData(array $data, string $operation = 'create'): array
    {
        return $data;
    }

    /**
     * Get validation rules for create operation
     * Override this method in child classes.
     */
    protected function getCreateRules(): array
    {
        return [];
    }

    /**
     * Get validation rules for update operation
     * Override this method in child classes.
     */
    protected function getUpdateRules(): array
    {
        return [];
    }

    /**
     * Transform data before saving
     * Override this method in child classes.
     */
    protected function transformData(array $data): array
    {
        return $data;
    }

    /**
     * Get paginated records with filters and sorting.
     */
    public function getPaginatedWithFilters(int $perPage = 15, array $filters = [], string $sortField = 'created_at', string $sortDirection = 'desc')
    {
        return $this->repository->getPaginatedWithFilters($perPage, $filters, $sortField, $sortDirection);
    }

    /**
     * Bulk delete records.
     */
    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }

    /**
     * Toggle status of a record.
     */
    public function toggleStatus(string $id)
    {
        return $this->repository->toggleStatus($id);
    }

    /**
     * Get records by status.
     */
    public function getByStatus(string $status)
    {
        return $this->repository->findBy(['status' => $status]);
    }
}
