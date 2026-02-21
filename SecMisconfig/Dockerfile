FROM php:8.2-apache

# VULN A05: Apache dengan directory listing SENGAJA diaktifkan
RUN echo "Options +Indexes" >> /etc/apache2/apache2.conf

# VULN A05: Error reporting verbose (tampilkan error ke browser)
RUN echo "display_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini

RUN docker-php-ext-install pdo pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# VULN A05: File backup dan konfigurasi sensitif bisa diakses
# (file .bak, .old, .env dibiarkan publik)

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
