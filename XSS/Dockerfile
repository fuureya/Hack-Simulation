FROM php:8.2-apache

# Install dependencies for SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Set permissions for development (including log.txt)
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 777 /var/www/html

# Initialize DB
RUN php /var/www/html/setup.php

EXPOSE 80
