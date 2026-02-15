# ---------------------------------------------------------
# Stage 1 – PHP 8.3 + Composer + vendor
# ---------------------------------------------------------
FROM php:8.3-fpm AS vendor

# Install dependencies untuk composer & gd
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts


# ---------------------------------------------------------
# Stage 2 – Build Laravel App (PHP 8.3)
# ---------------------------------------------------------
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath gd

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor

# Laravel optimize (hindari error env)
RUN php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

CMD ["php-fpm"]