# Dockerfile
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl \
    --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set appropriate permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 9001

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=9001"]