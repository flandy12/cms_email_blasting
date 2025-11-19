# -----------------------------------------
# Stage 1: Build dependencies + vendor
# -----------------------------------------
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# -----------------------------------------
# Stage 2: Build Laravel App
# -----------------------------------------
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath gd

# Copy PHP config (optional)
# COPY ./docker/php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/html

# Copy app source
COPY . .

# Copy vendor from build stage
COPY --from=vendor /app/vendor ./vendor

# Laravel optimizations
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

CMD ["php-fpm"]