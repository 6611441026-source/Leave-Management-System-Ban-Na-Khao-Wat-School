FROM php:8.3-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql mysqli zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html

EXPOSE 80

CMD sh -c "php -S 0.0.0.0:${PORT:-80} -t /var/www/html /var/www/html/router.php"
