<?php

namespace Modules\Base\tests\Feature;

use Modules\Base\Abstracts\BaseApiController;
use Modules\Base\Abstracts\BaseRepository;
use Modules\Base\Abstracts\BaseService;
use Modules\Base\Contracts\RepositoryInterface;
use Tests\TestCase;

class BaseApiControllerTest extends TestCase
{
    /**
     * Test BaseApiController methods exist.
     */
    public function test_base_api_controller_has_required_methods()
    {
        $controller = new class extends BaseApiController
        {
            protected function getService() {}

            protected function getResource(): string
            {
                return '';
            }

            protected function getResourceCollection(): string
            {
                return '';
            }

            protected function getStoreRequest(): string
            {
                return '';
            }

            protected function getUpdateRequest(): string
            {
                return '';
            }

            protected function getAllowedFilters(): array
            {
                return [];
            }
        };

        // Test methods exist by checking if they're callable
        $this->assertTrue(is_callable([$controller, 'index']));
        $this->assertTrue(is_callable([$controller, 'store'])); 
        $this->assertTrue(is_callable([$controller, 'show']));
        $this->assertTrue(is_callable([$controller, 'update']));
        $this->assertTrue(is_callable([$controller, 'destroy']));
        $this->assertTrue(is_callable([$controller, 'bulkDestroy']));
        $this->assertTrue(is_callable([$controller, 'toggleStatus']));
    }

    /**
     * Test BaseService implements required interface.
     */
    public function test_base_service_implements_interface()
    {
        $repository = $this->createMock(RepositoryInterface::class);

        $service = new class($repository) extends BaseService
        {
            // Concrete implementation for testing
        };

        $this->assertInstanceOf(BaseService::class, $service);
        $this->assertTrue(method_exists($service, 'getAll'));
        $this->assertTrue(method_exists($service, 'getById'));
        $this->assertTrue(method_exists($service, 'create'));
        $this->assertTrue(method_exists($service, 'update'));
        $this->assertTrue(method_exists($service, 'delete'));
        $this->assertTrue(method_exists($service, 'getPaginated'));
        $this->assertTrue(method_exists($service, 'getPaginatedWithFilters'));
        $this->assertTrue(method_exists($service, 'bulkDelete'));
        $this->assertTrue(method_exists($service, 'toggleStatus'));
    }

    /**
     * Test BaseRepository implements required interface.
     */
    public function test_base_repository_implements_interface()
    {
        // Create a mock model for testing
        $model = $this->createMock(\MongoDB\Laravel\Eloquent\Model::class);

        $repository = new class($model) extends BaseRepository
        {
            // Concrete implementation for testing
        };

        $this->assertInstanceOf(RepositoryInterface::class, $repository);
        $this->assertTrue(method_exists($repository, 'all'));
        $this->assertTrue(method_exists($repository, 'find'));
        $this->assertTrue(method_exists($repository, 'findOrFail'));
        $this->assertTrue(method_exists($repository, 'create'));
        $this->assertTrue(method_exists($repository, 'update'));
        $this->assertTrue(method_exists($repository, 'delete'));
        $this->assertTrue(method_exists($repository, 'paginate'));
        $this->assertTrue(method_exists($repository, 'findBy'));
        $this->assertTrue(method_exists($repository, 'findOneBy'));
        $this->assertTrue(method_exists($repository, 'count'));
        $this->assertTrue(method_exists($repository, 'exists'));
        $this->assertTrue(method_exists($repository, 'getPaginatedWithFilters'));
        $this->assertTrue(method_exists($repository, 'bulkDelete'));
        $this->assertTrue(method_exists($repository, 'toggleStatus'));
    }

    /**
     * Test success response format.
     */
    public function test_success_response_format()
    {
        $controller = new class extends BaseApiController
        {
            protected function getService() {}

            protected function getResource(): string
            {
                return '';
            }

            protected function getResourceCollection(): string
            {
                return '';
            }

            protected function getStoreRequest(): string
            {
                return '';
            }

            protected function getUpdateRequest(): string
            {
                return '';
            }

            protected function getAllowedFilters(): array
            {
                return [];
            }

            public function test_success_response()
            {
                return $this->successResponse(['id' => 1], 'Test success', 200);
            }
        };

        $response = $controller->test_success_response();
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($data['success']);
        $this->assertEquals('Test success', $data['message']);
        $this->assertEquals(['id' => 1], $data['data']);
    }

    /**
     * Test error response format.
     */
    public function test_error_response_format()
    {
        $controller = new class extends BaseApiController
        {
            protected function getService() {}

            protected function getResource(): string
            {
                return '';
            }

            protected function getResourceCollection(): string
            {
                return '';
            }

            protected function getStoreRequest(): string
            {
                return '';
            }

            protected function getUpdateRequest(): string
            {
                return '';
            }

            protected function getAllowedFilters(): array
            {
                return [];
            }

            public function test_error_response()
            {
                return $this->errorResponse('Test error', 400);
            }
        };

        $response = $controller->test_error_response();
        $data = $response->getData(true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($data['success']);
        $this->assertEquals('Test error', $data['message']);
    }
}
