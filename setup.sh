#!/bin/bash

# Laravel Modular Setup Script
# Usage: ./setup.sh [environment]
# Environment: development, staging, production

ENVIRONMENT=${1:-development}

echo "🚀 Setting up Laravel Modular for ${ENVIRONMENT} environment..."

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    if [ "$ENVIRONMENT" = "production" ]; then
        cp .env.production .env
    else
        cp .env.example .env
    fi
fi

# Install dependencies
echo "📦 Installing Composer dependencies..."
if [ "$ENVIRONMENT" = "production" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
else
    composer install
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public

# Set permissions (Linux/Mac)
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" ]]; then
    echo "🔐 Setting permissions..."
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache
fi

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run database setup for development
if [ "$ENVIRONMENT" = "development" ]; then
    echo "🗄️ Setting up database..."
    # Note: MongoDB doesn't require migrations, but we can run seeders if needed
    # php artisan db:seed
fi

# Install Node dependencies and build assets
if [ -f package.json ]; then
    echo "🎨 Installing Node dependencies..."
    npm install
    
    if [ "$ENVIRONMENT" = "production" ]; then
        echo "🏗️ Building production assets..."
        npm run build
    else
        echo "🏗️ Building development assets..."
        npm run dev
    fi
fi

# Create symbolic link for storage (if not exists)
if [ ! -L public/storage ]; then
    echo "🔗 Creating storage symbolic link..."
    php artisan storage:link
fi

# Optimize for production
if [ "$ENVIRONMENT" = "production" ]; then
    echo "⚡ Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Remove development files
    rm -f .env.example
    rm -rf tests/
    rm -rf .git/
fi

# Run tests for development
if [ "$ENVIRONMENT" = "development" ]; then
    echo "🧪 Running tests..."
    php artisan test --parallel
fi

echo "✅ Setup completed for ${ENVIRONMENT} environment!"

# Display next steps
echo ""
echo "🎉 Next steps:"
if [ "$ENVIRONMENT" = "development" ]; then
    echo "   • Start development server: php artisan serve"
    echo "   • Watch assets: npm run dev"
    echo "   • Run tests: php artisan test"
else
    echo "   • Configure your web server to point to public/ directory"
    echo "   • Set up process manager (PM2, Supervisor, etc.)"
    echo "   • Configure SSL certificate"
    echo "   • Set up monitoring and backups"
fi

echo "   • Health check: curl http://your-domain/health"
echo "   • Documentation: See CI-CD-README.md"
