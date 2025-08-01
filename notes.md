# Development Notes

## Environment
- **OS**: Windows
- **Shell**: PowerShell
- **Command separator**: `;` (not `&&` like Linux)

## Project Setup
- Laravel v12.21.0
- **Database: MongoDB** (localhost:27017)
- Database name: laravel2025_mongo
- No username/password required
- Development server: `php artisan serve`
- URL: http://127.0.0.1:8000

## MongoDB Configuration
- Package: `mongodb/laravel-mongodb`
- Host: 127.0.0.1
- Port: 27017
- Database: laravel2025_mongo
- Test route: http://127.0.0.1:8000/test-mongo

## PowerShell Commands
```powershell
# Example: Chain commands with semicolon
cd "path" ; php artisan serve
cd "path" ; composer install
```

## Module Architecture (DDD)

### Base Module
- **Abstracts**: BaseController, BaseModel, BaseRepository, BaseService
- **Contracts**: RepositoryInterface, ServiceInterface
- **Traits**: HasStatus, HasSlug
- **Enums**: StatusEnum
- **Exceptions**: ModuleException, ResourceNotFoundException

### Module Structure
```
modules/
├── Base/                    # Core module với interfaces & abstracts
├── [ModuleName]/           # Các module nghiệp vụ
│   ├── Controllers/        # Admin, API, Web controllers
│   ├── Models/            # Domain models  
│   ├── Repositories/      # Data access layer
│   ├── Services/          # Business logic
│   ├── Routes/            # web.php, admin.php, api.php
│   ├── Views/             # Blade templates
│   ├── tests/             # Unit, Feature, Browser tests
│   └── Database/          # Migrations, Seeders, Factories
```

### Route Structure
- **Web**: `/modulename/*` (chỉ render views)
- **Admin**: `/admin/modulename/*` (chỉ render admin views)  
- **API Public**: `/api/modulename/*`
- **API Admin**: `/api/admin/modulename/*`

### API-First Architecture
- **Frontend**: JavaScript/AJAX calls to API endpoints
- **No form POST**: All CRUD operations via API
- **Components**: BaseApiController, BaseResource, BaseRequest
- **Helpers**: api-helper.js cho frontend
- **Features**: 
  - Bulk operations
  - Status toggle
  - Advanced filtering
  - Pagination
  - Error handling

### Testing Structure
- **Unit Tests**: `modules/[Module]/tests/Unit/`
- **Feature Tests**: `modules/[Module]/tests/Feature/`
- **Config**: `modules/[Module]/tests/phpunit.xml`
- **Commands**: 
  - `php artisan test:module-base` (runs Base module tests)
  - PowerShell: `.\modules\Base\tests\run-module-tests.ps1`
- **16 tests, 81 assertions** ✅ passing

## CI/CD Pipeline  
- **GitHub Actions**: 1 simple workflow (CI only)
- **Testing**: PHP 8.2/8.3 matrix with PHPUnit tests
- **MongoDB**: Service container for database testing
- **Docker**: Multi-stage builds for dev/prod environments (kept for local dev)
- **Health Monitoring**: Comprehensive endpoints for production

### CI Simplified:
- ✅ Only PHPUnit testing (removed code quality, security, deployment)
- ✅ MongoDB extension v1.21.0 configured
- ✅ PHP 8.3 testing (user updated from 8.2/8.3 matrix)
- ✅ Sequential test execution (removed --parallel to avoid paratest dependency)
- ✅ Minimal dependencies, faster execution

### CI/CD Issues Fixed:
- ✅ Simplified from 3 complex workflows to 1 simple test workflow
- ✅ Removed unnecessary jobs (code-style, static-analysis, security-audit, deployment)
- ✅ MongoDB extension working properly
- ✅ Fast and reliable CI execution
- ✅ Fixed paratest requirement issue (removed --parallel from CI)

### Local Development:
- MongoDB extension: v1.21.0 (matches CI)
- Simple CI: Only tests, no code quality or deployment complexity
- Health checks: PHP-based using MongoDB\Driver\Manager

## PowerShell Commands Fix
```powershell
# ❌ KHÔNG hoạt động
curl -s http://127.0.0.1:8000/health | ConvertFrom-Json | ConvertTo-Json -Depth 3

# ✅ Sử dụng lệnh này
Invoke-RestMethod -Uri "http://127.0.0.1:8000/health" | ConvertTo-Json -Depth 3
```
