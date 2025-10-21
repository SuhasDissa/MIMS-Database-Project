# ----
# 1. Builder Stage: Install all dependencies & compile assets
# ----
# Use the PHP base image so we have both PHP/Composer and Node.js
FROM gcr.io/google-appengine/php82 AS builder

# Install Node.js 24.x and npm
RUN apt-get update && apt-get install -y curl gnupg \
    && curl -sL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean

WORKDIR /app

# 1. Install Composer dependencies (as requested)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 2. Install NPM dependencies
COPY package.json package-lock.json ./
RUN npm install

# 3. Copy the rest of the app code
COPY . .

# 4. Build frontend assets (now runs AFTER composer install)
RUN npm run build

# ----
# 2. Production Stage: Build the final PHP image
# ----
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

# Copy composer files
COPY composer.json composer.lock ./

# Copy the pre-installed vendor directory from the builder
COPY --from=builder /app/vendor ./vendor

# Copy the entire application code
COPY . .

# Copy the compiled assets from the "builder" stage
COPY --from=builder /app/public/build ./public/build

# Finish composer install (this will just optimize the autoloader)
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port 8080 (required by Cloud Run)
EXPOSE 8080

# Entrypoint: Start Laravel Octane
# This command runs your app
CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8080"]