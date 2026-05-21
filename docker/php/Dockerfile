FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git curl libpng-dev libxml2-dev oniguruma-dev libzip-dev zip unzip \
    icu-dev autoconf gcc g++ make \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && pecl install redis && docker-php-ext-enable redis \
    && apk del autoconf gcc g++ make

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize \
    && php artisan view:cache \
    && php artisan config:cache \
    && php artisan route:cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
