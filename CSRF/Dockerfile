FROM php:8.2-apache

# Install dependencies for SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

# Enable Apache mod_rewrite (optional but good for PHP apps)
RUN a2enmod rewrite

# Copy project files to the container
COPY . /var/www/html/

# Set permissions for SQLite database and directory
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Run setup.php to initialize the database during build or on startup
# We'll use a custom entrypoint to ensure it runs every time the container starts if needed, 
# but for simplicity, let's run it once here or via command.
RUN php /var/www/html/setup.php

# Expose port 80
EXPOSE 80
