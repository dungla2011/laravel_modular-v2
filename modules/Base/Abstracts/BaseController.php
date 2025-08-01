<?php

namespace Modules\Base\Abstracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Success response
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse($errors): JsonResponse
    {
        return $this->errorResponse('Validation failed', 422, $errors);
    }

    /**
     * Handle pagination parameters
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'page' => $request->get('page', 1),
            'per_page' => min($request->get('per_page', 15), 100), // Max 100 items per page
            'sort_field' => $request->get('sort_field', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc')
        ];
    }

    /**
     * Handle filter parameters
     */
    protected function getFilterParams(Request $request, array $allowedFilters = []): array
    {
        $filters = [];
        
        foreach ($allowedFilters as $filter) {
            if ($request->has($filter)) {
                $filters[$filter] = $request->get($filter);
            }
        }
        
        return $filters;
    }
}
