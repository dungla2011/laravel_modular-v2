# Laravel Modular Setup Script for Windows
# Usage: .\setup.ps1 [environment]
# Environment: development, staging, production

param(
    [Parameter()]
    [ValidateSet("development", "staging", "production")]
    [string]$Environment = "development"
)

Write-Host "🚀 Setting up Laravel Modular for $Environment environment..." -ForegroundColor Green

# Check if .env exists
if (-not (Test-Path .env)) {
    Write-Host "📝 Creating .env file..." -ForegroundColor Yellow
    if ($Environment -eq "production") {
        Copy-Item .env.production .env
    } else {
        Copy-Item .env.example .env
    }
}

# Install dependencies
Write-Host "📦 Installing Composer dependencies..." -ForegroundColor Yellow
if ($Environment -eq "production") {
    composer install --no-dev --optimize-autoloader --no-interaction
} else {
    composer install
}

# Generate application key if not set
$envContent = Get-Content .env -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
    php artisan key:generate
}

# Create storage directories
Write-Host "📁 Creating storage directories..." -ForegroundColor Yellow
$directories = @(
    "storage\framework\cache\data",
    "storage\framework\sessions", 
    "storage\framework\views",
    "storage\logs",
    "storage\app\public"
)

foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Force -Path $dir | Out-Null
    }
}

# Set permissions for Windows
Write-Host "🔐 Setting permissions..." -ForegroundColor Yellow
icacls "storage" /grant Everyone:F /T | Out-Null
icacls "bootstrap\cache" /grant Everyone:F /T | Out-Null

# Clear caches
Write-Host "🧹 Clearing caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run database setup for development
if ($Environment -eq "development") {
    Write-Host "🗄️ Setting up database..." -ForegroundColor Yellow
    # Note: MongoDB doesn't require migrations, but we can run seeders if needed
    # php artisan db:seed
}

# Install Node dependencies and build assets
if (Test-Path package.json) {
    Write-Host "🎨 Installing Node dependencies..." -ForegroundColor Yellow
    npm install
    
    if ($Environment -eq "production") {
        Write-Host "🏗️ Building production assets..." -ForegroundColor Yellow
        npm run build
    } else {
        Write-Host "🏗️ Building development assets..." -ForegroundColor Yellow
        npm run dev
    }
}

# Create symbolic link for storage (if not exists)
if (-not (Test-Path "public\storage")) {
    Write-Host "🔗 Creating storage symbolic link..." -ForegroundColor Yellow
    php artisan storage:link
}

# Optimize for production
if ($Environment -eq "production") {
    Write-Host "⚡ Optimizing for production..." -ForegroundColor Yellow
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Remove development files
    if (Test-Path .env.example) { Remove-Item .env.example }
    if (Test-Path tests) { Remove-Item -Recurse -Force tests }
    if (Test-Path .git) { Remove-Item -Recurse -Force .git }
}

# Run tests for development
if ($Environment -eq "development") {
    Write-Host "🧪 Running tests..." -ForegroundColor Yellow
    php artisan test --parallel
}

Write-Host "✅ Setup completed for $Environment environment!" -ForegroundColor Green

# Display next steps
Write-Host ""
Write-Host "🎉 Next steps:" -ForegroundColor Cyan
if ($Environment -eq "development") {
    Write-Host "   • Start development server: php artisan serve" -ForegroundColor White
    Write-Host "   • Watch assets: npm run dev" -ForegroundColor White
    Write-Host "   • Run tests: php artisan test" -ForegroundColor White
} else {
    Write-Host "   • Configure your web server to point to public/ directory" -ForegroundColor White
    Write-Host "   • Set up process manager (PM2, Supervisor, etc.)" -ForegroundColor White
    Write-Host "   • Configure SSL certificate" -ForegroundColor White
    Write-Host "   • Set up monitoring and backups" -ForegroundColor White
}

Write-Host "   • Health check (PowerShell): Invoke-RestMethod -Uri `"http://127.0.0.1:8000/health`" | ConvertTo-Json -Depth 3" -ForegroundColor White
Write-Host "   • Health check (curl): curl http://your-domain/health" -ForegroundColor White
Write-Host "   • Documentation: See docs/PowerShell-Commands.md for Windows-specific commands" -ForegroundColor White
Write-Host ""
Write-Host "⚠️  Windows PowerShell Note:" -ForegroundColor Yellow  
Write-Host "   Use 'Invoke-RestMethod' instead of 'curl -s' for API testing" -ForegroundColor Yellow
