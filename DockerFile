# Use PHP 8.2 with CLI
FROM php:8.2-cli

# Install system dependencies and Node.js
RUN apt-get update && apt-get install -y \
    git zip unzip libzip-dev npm sqlite3 libsqlite3-dev && \
    docker-php-ext-install pdo pdo_sqlite

# Set working directory
WORKDIR /app

# Copy project files
COPY . /app

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build Vue frontend (for Inertia)
RUN npm ci && npm run build

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose port 10000
EXPOSE 10000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
