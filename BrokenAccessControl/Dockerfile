FROM php:8.2-apache

RUN a2enmod rewrite

# No extra extensions needed for this lab as it uses sessions/arrays
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80
