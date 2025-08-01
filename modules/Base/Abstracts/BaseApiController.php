<?php

namespace Modules\Base\Abstracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseApiController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $pagination = $this->getPaginationParams($request);
            $filters = $this->getFilterParams($request, $this->getAllowedFilters());

            $data = $this->getService()->getPaginatedWithFilters(
                $pagination['per_page'],
                $filters,
                $pagination['sort_field'],
                $pagination['sort_direction']
            );

            return $this->successResponse(
                $this->getResourceCollection()::make($data),
                'Data retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve data: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->getStoreRequest()::createFrom($request)->getValidatedData();
            $record = $this->getService()->create($data);

            return $this->successResponse(
                $this->getResource()::make($record),
                'Resource created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create resource: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $record = $this->getService()->getById($id);

            if (! $record) {
                return $this->notFoundResponse();
            }

            return $this->successResponse(
                $this->getResource()::make($record),
                'Resource retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve resource: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $data = $this->getUpdateRequest()::createFrom($request)->getValidatedData();
            $record = $this->getService()->update($id, $data);

            return $this->successResponse(
                $this->getResource()::make($record),
                'Resource updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update resource: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->getService()->delete($id);

            if (! $deleted) {
                return $this->notFoundResponse();
            }

            return $this->successResponse(null, 'Resource deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete resource: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete resources.
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return $this->errorResponse('No IDs provided');
            }

            $deleted = $this->getService()->bulkDelete($ids);

            return $this->successResponse(
                ['deleted_count' => $deleted],
                "Successfully deleted {$deleted} resources"
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to bulk delete: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status of the resource.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $record = $this->getService()->toggleStatus($id);

            return $this->successResponse(
                $this->getResource()::make($record),
                'Status updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to toggle status: ' . $e->getMessage());
        }
    }

    // Abstract methods that must be implemented by child classes
    abstract protected function getService();

    abstract protected function getResource(): string;

    abstract protected function getResourceCollection(): string;

    abstract protected function getStoreRequest(): string;

    abstract protected function getUpdateRequest(): string;

    abstract protected function getAllowedFilters(): array;
}
