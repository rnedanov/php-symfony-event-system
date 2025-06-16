FROM php:8.3-apache

# Установка зависимостей и расширений PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install \
        intl \
        mbstring \
        pdo_mysql \
        zip

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Настройка Apache
RUN a2enmod rewrite
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Рабочая директория
WORKDIR /srv/app