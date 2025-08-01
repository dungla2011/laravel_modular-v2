# CI/CD Pipeline

This project includes a comprehensive CI/CD pipeline using GitHub Actions for automated testing, quality checks, and deployment.

## 🚀 Pipeline Overview

### Workflows

1. **CI/CD Pipeline** (`.github/workflows/ci.yml`)
   - Runs on push to `main` and `develop` branches
   - Runs on pull requests
   - Tests against PHP 8.2 and 8.3
   - Includes MongoDB service

2. **Code Quality** (`.github/workflows/code-quality.yml`)
   - Code style checks with Laravel Pint
   - Static analysis with PHPStan
   - Module-specific testing
   - Dependency auditing

3. **Deploy** (`.github/workflows/deploy.yml`)
   - Automatic deployment to staging (develop branch)
   - Production deployment with manual approval (main branch)
   - Rollback capabilities

## 🔧 Setup Instructions

### 1. GitHub Secrets

Add these secrets to your GitHub repository:

#### Staging Environment
```
STAGING_HOST=your-staging-server.com
STAGING_USER=deploy
STAGING_SSH_KEY=your-private-ssh-key
STAGING_PORT=22
STAGING_DEPLOY_URL=https://your-deploy-server.com
STAGING_URL=https://staging.your-app.com
```

#### Production Environment
```
PRODUCTION_HOST=your-production-server.com
PRODUCTION_USER=deploy
PRODUCTION_SSH_KEY=your-private-ssh-key
PRODUCTION_PORT=22
PRODUCTION_DEPLOY_URL=https://your-deploy-server.com
PRODUCTION_URL=https://your-app.com
MAINTENANCE_SECRET=your-secret-for-maintenance-mode
```

### 2. Server Setup

#### SSH Key Generation
```bash
# Generate SSH key pair
ssh-keygen -t rsa -b 4096 -C "deploy@github-actions"

# Copy public key to servers
ssh-copy-id -i ~/.ssh/id_rsa.pub deploy@your-server.com
```

#### Server Directory Structure
```
/var/www/
├── staging/
│   ├── current -> releases/20240801-123456
│   ├── releases/
│   └── backup-20240801-120000/
└── production/
    ├── current -> releases/20240801-123456
    ├── releases/
    └── backup-20240801-120000/
```

### 3. Environment Configuration

#### Local Development
```bash
# Copy environment file
cp .env.example .env

# Update database configuration
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=laravel2025_mongo
```

#### Staging/Production
```bash
# Additional production settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.com

# Cache configuration
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# MongoDB with authentication
DB_USERNAME=your-mongo-user
DB_PASSWORD=your-mongo-password
```

## 🐳 Docker Deployment

### Development
```bash
# Build and start containers
docker-compose up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate

# Build assets
docker-compose exec node npm run dev
```

### Production
```bash
# Build production containers
docker-compose -f docker-compose.prod.yml up -d

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Cache configuration
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

## 🔍 Quality Checks

### Code Style
```bash
# Check code style
vendor/bin/pint --test

# Fix code style
vendor/bin/pint
```

### Static Analysis
```bash
# Run PHPStan
vendor/bin/phpstan analyse
```

### Testing
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific module tests
php artisan test modules/Base/Tests
```

### Security Audit
```bash
# Check for security vulnerabilities
composer audit
```

## 📊 Monitoring & Health Checks

### Health Check Endpoints

- **Basic Health**: `GET /health`
- **Liveness Probe**: `GET /health/live`
- **Readiness Probe**: `GET /health/ready`

### Health Check Response
```json
{
  "status": "healthy",
  "timestamp": "2024-08-01T12:00:00.000000Z",
  "checks": {
    "app": {"status": "ok", "message": "Application is running"},
    "database": {"status": "ok", "message": "Database connection successful"},
    "cache": {"status": "ok", "message": "Cache is working"},
    "storage": {"status": "ok", "message": "Storage is working"}
  },
  "version": "1.0.0",
  "environment": "production"
}
```

## 🚦 Pipeline Stages

### 1. Test Stage
- ✅ PHP 8.2 & 8.3 compatibility
- ✅ MongoDB service connection
- ✅ Dependency installation
- ✅ Unit & Feature tests
- ✅ Code coverage reporting

### 2. Security Stage
- ✅ Dependency vulnerability scan
- ✅ Security audit

### 3. Quality Stage
- ✅ Code style validation
- ✅ Static analysis
- ✅ Module-specific tests

### 4. Build Stage
- ✅ Optimized dependency installation
- ✅ Asset compilation
- ✅ Deployment artifact creation

### 5. Deploy Stage
- ✅ Zero-downtime deployment
- ✅ Database migrations
- ✅ Cache optimization
- ✅ Health checks
- ✅ Automatic rollback on failure

## 🔄 Deployment Process

### Staging Deployment (Automatic)
1. Triggered on push to `develop` branch
2. Runs all quality checks
3. Builds and deploys to staging
4. Runs smoke tests

### Production Deployment (Manual Approval)
1. Triggered on push to `main` branch
2. Requires manual approval in GitHub
3. Zero-downtime deployment
4. Automatic rollback if health checks fail

### Rollback Process
```bash
# Manual rollback (if needed)
cd /var/www/production
php current/artisan down
ln -sfn backup-20240801-120000 current
sudo systemctl reload php8.2-fpm nginx
php current/artisan up
```

## 📁 File Structure

```
.github/workflows/
├── ci.yml              # Main CI/CD pipeline
├── code-quality.yml    # Code quality checks
└── deploy.yml          # Deployment workflows

docker/
├── nginx/
│   └── conf.d/
│       └── default.conf
└── php/
    ├── local.ini       # Development PHP config
    └── production.ini  # Production PHP config

.deployignore           # Files to exclude from deployment
phpstan.neon           # Static analysis configuration
pint.json              # Code style configuration
docker-compose.yml     # Development containers
docker-compose.prod.yml # Production containers
Dockerfile             # Multi-stage container build
```

## 🛠️ Customization

### Adding New Checks
Add new steps to `.github/workflows/code-quality.yml`:

```yaml
- name: Custom Check
  run: |
    # Your custom validation here
    ./scripts/custom-check.sh
```

### Module-Specific Testing
Tests are automatically discovered in `modules/*/Tests/` directories.

### Environment-Specific Configuration
Use environment variables and Laravel's configuration system for different environments.

## 🐛 Troubleshooting

### Common Issues

1. **MongoDB Connection Failed**
   - Check MongoDB service is running
   - Verify connection credentials
   - Ensure network connectivity

2. **Asset Build Failures**
   - Clear npm cache: `npm cache clean --force`
   - Remove node_modules: `rm -rf node_modules && npm install`

3. **Permission Issues**
   - Check file permissions: `chmod -R 755 storage bootstrap/cache`
   - Verify ownership: `chown -R www-data:www-data /var/www/html`

4. **Memory Issues**
   - Increase PHP memory limit in `docker/php/*.ini`
   - Optimize Composer: `composer install --optimize-autoloader --no-dev`

### Debugging Deployments

1. **Check deployment logs**:
   ```bash
   # GitHub Actions logs
   # Go to Actions tab in GitHub repository
   ```

2. **SSH to server**:
   ```bash
   ssh deploy@your-server.com
   cd /var/www/production/current
   tail -f storage/logs/laravel.log
   ```

3. **Check health status**:
   ```bash
   curl https://your-app.com/health
   ```
