# ----
# 1. Builder Stage: Install all dependencies & compile assets
# ----
# Use the official php:8.2-fpm image as a base (it's Debian-based)
FROM php:8.2-fpm AS builder

# Install Node.js 24.x, npm, and other system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    gnupg \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    && curl -sL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1. Install Composer dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 2. Install NPM dependencies
COPY package.json package-lock.json ./
RUN npm install

# 3. Copy the rest of the app code
COPY . .

# 4. Build frontend assets
RUN npm run build

# ----
# 2. Production Stage: Build the final PHP image
# ----
FROM php:8.2-fpm

# Install system dependencies needed for extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    default-libmysqlclient-dev \
    libbrotli-dev \
    && apt-get clean

# Install core PHP extensions required by Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    zip \
    bcmath \
    sockets

# Install Swoole (for Octane) and Redis via PECL
# This will now find libbrotlienc and compile successfully
RUN pecl install swoole redis \
    && docker-php-ext-enable swoole redis

WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Copy the pre-installed vendor directory from the builder
COPY --from=builder /app/vendor ./vendor

# Copy the entire application code (excluding files from .dockerignore)
COPY . .

# Copy the compiled assets from the "builder" stage
COPY --from=builder /app/public/build ./public/build

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Finish composer install (this will just optimize the autoloader)
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port 8080 (required by Cloud Run)
EXPOSE 8080

# Entrypoint: Start Laravel Octane
# This command runs your app
CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8080"]