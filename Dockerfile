FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    default-mysql-client \
    libpng-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql mysqli zip gd \
    && rm -f /etc/apache2/mods-enabled/mpm_event.load \
             /etc/apache2/mods-enabled/mpm_event.conf \
             /etc/apache2/mods-enabled/mpm_worker.load \
             /etc/apache2/mods-enabled/mpm_worker.conf \
    && a2enmod mpm_prefork rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html

EXPOSE 80
