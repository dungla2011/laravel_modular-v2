<?php

namespace Modules\Base\tests\Unit;

use Modules\Base\Enums\StatusEnum;
use PHPUnit\Framework\TestCase;

class BaseModuleComponentsTest extends TestCase
{
    /**
     * Test StatusEnum functionality.
     */
    public function test_status_enum_has_correct_values()
    {
        $expectedValues = ['active', 'inactive', 'pending', 'draft', 'published', 'deleted'];
        $actualValues = StatusEnum::values();

        $this->assertEquals($expectedValues, $actualValues);
    }

    /**
     * Test StatusEnum labels.
     */
    public function test_status_enum_labels()
    {
        $this->assertEquals('Hoạt động', StatusEnum::ACTIVE->label());
        $this->assertEquals('Không hoạt động', StatusEnum::INACTIVE->label());
        $this->assertEquals('Chờ duyệt', StatusEnum::PENDING->label());
        $this->assertEquals('Bản nháp', StatusEnum::DRAFT->label());
        $this->assertEquals('Đã xuất bản', StatusEnum::PUBLISHED->label());
        $this->assertEquals('Đã xóa', StatusEnum::DELETED->label());
    }

    /**
     * Test StatusEnum colors.
     */
    public function test_status_enum_colors()
    {
        $this->assertEquals('success', StatusEnum::ACTIVE->color());
        $this->assertEquals('secondary', StatusEnum::INACTIVE->color());
        $this->assertEquals('warning', StatusEnum::PENDING->color());
        $this->assertEquals('info', StatusEnum::DRAFT->color());
        $this->assertEquals('primary', StatusEnum::PUBLISHED->color());
        $this->assertEquals('danger', StatusEnum::DELETED->color());
    }

    /**
     * Test that all base abstract classes exist.
     */
    public function test_abstract_classes_exist()
    {
        $classes = [
            'Modules\Base\Abstracts\BaseController',
            'Modules\Base\Abstracts\BaseApiController',
            'Modules\Base\Abstracts\BaseModel',
            'Modules\Base\Abstracts\BaseRepository',
            'Modules\Base\Abstracts\BaseService',
            'Modules\Base\Abstracts\BaseResource',
            'Modules\Base\Abstracts\BaseResourceCollection',
            'Modules\Base\Abstracts\BaseRequest',
        ];

        foreach ($classes as $class) {
            $this->assertTrue(class_exists($class), "Class {$class} should exist");
        }
    }

    /**
     * Test that all interfaces exist.
     */
    public function test_interfaces_exist()
    {
        $interfaces = [
            'Modules\Base\Contracts\RepositoryInterface',
            'Modules\Base\Contracts\ServiceInterface',
        ];

        foreach ($interfaces as $interface) {
            $this->assertTrue(interface_exists($interface), "Interface {$interface} should exist");
        }
    }

    /**
     * Test that all traits exist.
     */
    public function test_traits_exist()
    {
        $traits = [
            'Modules\Base\Traits\HasStatus',
            'Modules\Base\Traits\HasSlug',
        ];

        foreach ($traits as $trait) {
            $this->assertTrue(trait_exists($trait), "Trait {$trait} should exist");
        }
    }

    /**
     * Test that all exception classes exist.
     */
    public function test_exception_classes_exist()
    {
        $exceptions = [
            'Modules\Base\Exceptions\ModuleException',
            'Modules\Base\Exceptions\ResourceNotFoundException',
        ];

        foreach ($exceptions as $exception) {
            $this->assertTrue(class_exists($exception), "Exception {$exception} should exist");
        }
    }

    /**
     * Test that middleware classes exist.
     */
    public function test_middleware_classes_exist()
    {
        $middleware = [
            'Modules\Base\Middleware\ApiResponseMiddleware',
        ];

        foreach ($middleware as $class) {
            $this->assertTrue(class_exists($class), "Middleware {$class} should exist");
        }
    }

    /**
     * Test that api-helper.js asset exists.
     */
    public function test_api_helper_asset_exists()
    {
        $assetPath = dirname(__DIR__) . '/../Assets/api-helper.js';
        $this->assertFileExists($assetPath, 'api-helper.js should exist');
    }

    /**
     * Test Base module directory structure.
     */
    public function test_base_module_directory_structure()
    {
        $basePath = dirname(__DIR__) . '/..';

        $requiredDirectories = [
            'Abstracts',
            'Contracts',
            'Traits',
            'Enums',
            'Exceptions',
            'Middleware',
            'Assets',
            'tests',
            'tests/Unit',
            'tests/Feature',
        ];

        foreach ($requiredDirectories as $dir) {
            $fullPath = $basePath . '/' . $dir;
            $this->assertDirectoryExists($fullPath, "Directory {$dir} should exist in Base module");
        }
    }

    /**
     * Test required files exist.
     */
    public function test_required_files_exist()
    {
        $basePath = dirname(__DIR__) . '/..';

        $requiredFiles = [
            'ModuleServiceProvider.php',
            'README.md',
            'MODULE_TEMPLATE.md',
        ];

        foreach ($requiredFiles as $file) {
            $fullPath = $basePath . '/' . $file;
            $this->assertFileExists($fullPath, "File {$file} should exist in Base module");
        }
    }
}
