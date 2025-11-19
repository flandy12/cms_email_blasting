# -----------------------------------------
# Stage 1 – Composer + Vendor
# -----------------------------------------
FROM php:8.2-fpm AS vendor

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install zip gd mbstring

WORKDIR /app

COPY composer.json composer.lock ./
RUN php -d memory_limit=-1 /usr/local/bin/composer install --no-dev --prefer-dist --optimize-autoloader

# -----------------------------------------
# Stage 2 – Build Laravel App
# -----------------------------------------
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath gd

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

CMD ["php-fpm"]