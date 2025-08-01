# Module Structure Template

Cấu trúc chuẩn cho mỗi module trong hệ thống DDD:

```
ModuleName/
├── Controllers/
│   ├── Admin/
│   │   └── ModuleNameController.php     # Admin controller
│   ├── Api/
│   │   ├── Admin/
│   │   │   └── ModuleNameController.php # Admin API controller  
│   │   └── ModuleNameController.php     # Public API controller
│   └── Web/
│       └── ModuleNameController.php     # Web controller
├── Models/
│   └── ModuleName.php                   # Main model
├── Repositories/
│   ├── Contracts/
│   │   └── ModuleNameRepositoryInterface.php
│   └── ModuleNameRepository.php         # Repository implementation
├── Services/
│   ├── Contracts/
│   │   └── ModuleNameServiceInterface.php  
│   └── ModuleNameService.php            # Service implementation
├── Requests/
│   ├── CreateModuleNameRequest.php      # Validation cho create
│   └── UpdateModuleNameRequest.php      # Validation cho update
├── Resources/
│   ├── ModuleNameResource.php           # API resource
│   └── ModuleNameCollection.php         # API resource collection
├── Routes/
│   ├── web.php                          # Web routes (/modulename/...)
│   ├── admin.php                        # Admin routes (/admin/modulename/...)
│   └── api.php                          # API routes (/api/modulename/...)
├── Views/
│   ├── admin/
│   │   ├── index.blade.php             # Admin list view
│   │   ├── create.blade.php            # Admin create view
│   │   ├── edit.blade.php              # Admin edit view
│   │   └── show.blade.php              # Admin detail view
│   └── web/
│       ├── index.blade.php             # Public list view
│       └── show.blade.php              # Public detail view
├── Database/
│   ├── Migrations/
│   │   └── create_modulenames_collection.php
│   ├── Seeders/
│   │   └── ModuleNameSeeder.php
│   └── Factories/
│       └── ModuleNameFactory.php
├── Tests/
│   ├── Unit/
│   │   ├── ModuleNameRepositoryTest.php
│   │   └── ModuleNameServiceTest.php
│   ├── Feature/
│   │   ├── ModuleNameApiTest.php
│   │   └── ModuleNameWebTest.php
│   └── Browser/
│       └── ModuleNameTest.php           # Dusk tests
├── Policies/
│   └── ModuleNamePolicy.php             # Authorization policies
├── Events/
│   ├── ModuleNameCreated.php
│   ├── ModuleNameUpdated.php
│   └── ModuleNameDeleted.php
├── Listeners/
│   └── ModuleNameEventListener.php
├── Jobs/
│   └── ProcessModuleNameJob.php         # Background jobs
├── Notifications/
│   └── ModuleNameNotification.php
├── Observers/
│   └── ModuleNameObserver.php
├── ModuleNameServiceProvider.php        # Module service provider
└── README.md                           # Module documentation
```

## Route Examples

### Web Routes (/modules/ModuleName/Routes/web.php)
```php
Route::prefix('modulename')->group(function () {
    Route::get('/', [Web\ModuleNameController::class, 'index'])->name('modulename.index');
    Route::get('/{slug}', [Web\ModuleNameController::class, 'show'])->name('modulename.show');
});
```

### Admin Routes (/modules/ModuleName/Routes/admin.php)
```php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::prefix('modulename')->name('admin.modulename.')->group(function () {
        Route::get('/', [Admin\ModuleNameController::class, 'index'])->name('index');
        Route::get('/create', [Admin\ModuleNameController::class, 'create'])->name('create');
        Route::post('/', [Admin\ModuleNameController::class, 'store'])->name('store');
        Route::get('/{id}', [Admin\ModuleNameController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [Admin\ModuleNameController::class, 'edit'])->name('edit');
        Route::put('/{id}', [Admin\ModuleNameController::class, 'update'])->name('update');
        Route::delete('/{id}', [Admin\ModuleNameController::class, 'destroy'])->name('destroy');
    });
});
```

### API Routes (/modules/ModuleName/Routes/api.php)
```php
Route::prefix('api')->group(function () {
    // Public API
    Route::prefix('modulename')->group(function () {
        Route::get('/', [Api\ModuleNameController::class, 'index']);
        Route::get('/{id}', [Api\ModuleNameController::class, 'show']);
        Route::get('/search', [Api\ModuleNameController::class, 'search']);
    });
    
    // Admin API
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::apiResource('modulename', Api\Admin\ModuleNameController::class);
        Route::post('modulename/bulk-delete', [Api\Admin\ModuleNameController::class, 'bulkDestroy']);
        Route::post('modulename/{id}/toggle-status', [Api\Admin\ModuleNameController::class, 'toggleStatus']);
    });
});
```

## API-First Approach

### Frontend Integration
```html
<!-- Include API Helper -->
<script src="/modules/Base/Assets/api-helper.js"></script>

<script>
// Initialize module CRUD
const newsCrud = new ModuleCrud('news');

// Example: Load data with AJAX
async function loadNews() {
    try {
        const response = await newsCrud.getAll({
            page: 1,
            per_page: 10,
            status: 'published'
        });
        
        updateNewsTable(response.data);
    } catch (error) {
        UiHelper.showError(error.message);
    }
}

// Example: Create new record
async function createNews(formData) {
    const button = document.getElementById('submit-btn');
    
    try {
        UiHelper.showLoading(button, 'Creating...');
        
        const response = await newsCrud.create(formData);
        UiHelper.showSuccess('News created successfully!');
        
        // Reload table or redirect
        loadNews();
    } catch (error) {
        UiHelper.showError(error.message, error.data?.errors);
    } finally {
        UiHelper.hideLoading(button, 'Create News');
    }
}
</script>
```

### Controller Example
```php
// Api/Admin/NewsController.php
class NewsController extends BaseApiController
{
    protected function getService(): NewsService
    {
        return app(NewsService::class);
    }
    
    protected function getResource(): string
    {
        return NewsResource::class;
    }
    
    protected function getResourceCollection(): string
    {
        return NewsCollection::class;
    }
    
    protected function getStoreRequest(): string
    {
        return CreateNewsRequest::class;
    }
    
    protected function getUpdateRequest(): string
    {
        return UpdateNewsRequest::class;
    }
    
    protected function getAllowedFilters(): array
    {
        return ['title', 'status', 'category_id', 'author_id'];
    }
}
```

### Request Validation Example
```php
// Requests/CreateNewsRequest.php
class CreateNewsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,pending',
            'category_id' => 'required|exists:categories,_id',
        ];
    }
    
    public function attributes(): array
    {
        return [
            'title' => 'Tiêu đề',
            'content' => 'Nội dung',
            'status' => 'Trạng thái',
            'category_id' => 'Danh mục',
        ];
    }
}
```

### Resource Example
```php
// Resources/NewsResource.php
class NewsResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            ...parent::toArray($request),
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
            'status_label' => StatusEnum::from($this->status)->label(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'author' => new UserResource($this->whenLoaded('author')),
        ];
    }
}
```
