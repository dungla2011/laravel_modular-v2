# Base Module

Module Base cung cấp các interface, abstract class và trait chung cho tất cả các module khác.

## Cấu trúc

```
Base/
├── Abstracts/
│   ├── BaseController.php     # Controller cơ bản với các method tiện ích
│   ├── BaseModel.php          # Model cơ bản cho MongoDB
│   ├── BaseRepository.php     # Repository cơ bản với CRUD operations
│   └── BaseService.php        # Service cơ bản với business logic
├── Contracts/
│   ├── RepositoryInterface.php # Interface cho Repository
│   └── ServiceInterface.php    # Interface cho Service
├── Traits/
│   ├── HasStatus.php          # Trait quản lý status
│   └── HasSlug.php           # Trait tự động tạo slug
├── Enums/
│   └── StatusEnum.php        # Enum cho các trạng thái
├── Exceptions/
│   ├── ModuleException.php            # Exception cơ bản
│   └── ResourceNotFoundException.php  # Exception cho resource không tìm thấy
└── ModuleServiceProvider.php  # Service Provider tự động load modules
```

## Sử dụng

### 1. Repository Pattern

```php
use Modules\Base\Abstracts\BaseRepository;
use Modules\Base\Contracts\RepositoryInterface;

class UserRepository extends BaseRepository implements RepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
    
    // Thêm các method riêng cho User
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }
}
```

### 2. Service Pattern

```php
use Modules\Base\Abstracts\BaseService;

class UserService extends BaseService
{
    protected function validateData(array $data, string $operation = 'create'): array
    {
        $rules = $operation === 'create' ? $this->getCreateRules() : $this->getUpdateRules();
        
        return validator($data, $rules)->validate();
    }
    
    protected function getCreateRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ];
    }
}
```

### 3. Controller Pattern

```php
use Modules\Base\Abstracts\BaseController;

class UserController extends BaseController
{
    public function index(Request $request)
    {
        $users = $this->userService->getPaginated($request->get('per_page', 15));
        return $this->successResponse($users);
    }
    
    public function store(Request $request)
    {
        try {
            $user = $this->userService->create($request->all());
            return $this->successResponse($user, 'User created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
```

### 4. Model với Traits

```php
use Modules\Base\Abstracts\BaseModel;
use Modules\Base\Traits\HasStatus;
use Modules\Base\Traits\HasSlug;

class Post extends BaseModel
{
    use HasStatus, HasSlug;
    
    protected $collection = 'posts';
    
    protected $fillable = [
        'title', 'content', 'status', 'slug'
    ];
}
```

## Tính năng

### BaseController
- `successResponse()` - Response thành công
- `errorResponse()` - Response lỗi 
- `notFoundResponse()` - Response 404
- `getPaginationParams()` - Xử lý pagination
- `getFilterParams()` - Xử lý filters

### BaseRepository  
- CRUD operations cơ bản
- Pagination
- Find by criteria
- Count, exists methods

### HasStatus Trait
- `activate()`, `deactivate()`
- `isActive()`, `isInactive()`
- Scopes: `active()`, `inactive()`, `pending()`

### HasSlug Trait
- Tự động tạo slug từ title
- Hỗ trợ tiếng Việt
- `findBySlug()` method
- Unique slug generation
