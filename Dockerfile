# ----
# 1. Builder Stage: Compile frontend assets
# ----
FROM node:18-alpine AS builder

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run prod

# ----
# 2. Production Stage: Build the final PHP image
# ----
# Use the official Google Cloud PHP 8.2 image
FROM gcr.io/google-appengine/php82

# Install PHP extensions required by Laravel & Octane
RUN apt-get update && apt-get install -y \
    swoole \
    php8.2-swoole \
    php8.2-mysql \
    php8.2-pgsql \
    php8.2-redis \
    php8.2-zip \
    php8.2-bcmath \
    && apt-get clean

WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy the entire application code
COPY . .

# Copy the compiled assets from the "builder" stage
COPY --from=builder /app/public/build ./public/build

# Finish composer install
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port 8080 (required by Cloud Run)
EXPOSE 8080

# Entrypoint: Start Laravel Octane
# This command runs your app
CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8080"]