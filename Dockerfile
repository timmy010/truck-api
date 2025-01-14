# Используем базовый образ PHP
FROM php:8.2-fpm

# Устанавливаем системные зависимости и PHP-расширения
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip sockets

# Устанавливаем расширение для Redis
RUN pecl install redis && docker-php-ext-enable redis

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем содержимое локальной директории в контейнер
COPY ./app /var/www/html

# Устанавливаем права на рабочую директорию
RUN chown -R www-data:www-data /var/www/html

# Устанавливаем зависимости проекта с помощью Composer
RUN composer install

# Указываем, что контейнер будет использовать сеть app-network
EXPOSE 9000