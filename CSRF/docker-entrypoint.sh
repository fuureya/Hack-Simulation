#!/bin/bash
# Wait for MariaDB to be ready

echo "Waiting for MariaDB to be ready..."

until nc -z labsec-db 3306; do
  echo "MariaDB is unavailable - sleeping"
  sleep 2
done

echo "MariaDB is up - initializing database"
php /var/www/html/setup.php

echo "Starting Apache..."
apache2-foreground
