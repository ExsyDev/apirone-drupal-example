# Dockerfile
FROM drupal:10-php8.3-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    unzip git && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install bcmath gd opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./drupal.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html
