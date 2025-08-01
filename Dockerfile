# Multi-stage build
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    openssl-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Copy application code
COPY . .

# Generate autoload files
RUN composer dump-autoload --optimize --no-dev

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Production build stage
FROM base AS production

# Install Node.js for asset building
RUN apk add --no-cache nodejs npm

# Copy package files
COPY package.json package-lock.json ./

# Install Node dependencies and build assets
RUN npm ci --only=production \
    && npm run build \
    && npm cache clean --force \
    && rm -rf node_modules

# Remove Node.js to reduce image size
RUN apk del nodejs npm

# Copy optimized autoloader
RUN composer install --optimize-autoloader --no-dev

# Create non-root user
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Switch to non-root user
USER www

# Expose port
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php-fpm-healthcheck || exit 1

# Start PHP-FPM
CMD ["php-fpm"]

# Development build stage
FROM base AS development

# Install additional development tools
RUN apk add --no-cache \
    bash \
    mysql-client \
    postgresql-client

# Install XDebug for debugging
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy XDebug configuration
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install all Composer dependencies (including dev)
RUN composer install

# Install Node.js
RUN apk add --no-cache nodejs npm

# Copy package files and install Node dependencies
COPY package.json package-lock.json ./
RUN npm install

# Don't change user in development for easier file permissions
CMD ["php-fpm"]
