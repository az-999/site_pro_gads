FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libmemcached-dev \
    zlib1g-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY composer.json composer.lock* ./

RUN composer install --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true

COPY . .

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
