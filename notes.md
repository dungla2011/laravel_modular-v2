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
