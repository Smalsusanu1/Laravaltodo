FROM php:8.1-fpm

WORKDIR /var/www
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www
USER www-data

EXPOSE 9000
CMD ["php-fpm"]