FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pgsql pdo_pgsql zip sockets

RUN pecl install redis && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY app/composer.json .  

RUN chown -R www-data:www-data /var/www/html

RUN composer install

EXPOSE 9000