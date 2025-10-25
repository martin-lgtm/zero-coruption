# Use PHP 8.2 with CLI
FROM php:8.2-cli

# System deps + Node + SQLite + PHP extensions
RUN apt-get update && apt-get install -y \
    git zip unzip libzip-dev npm sqlite3 libsqlite3-dev \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip

# Workdir
WORKDIR /app

# App files
COPY . /app

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Build frontend (Vite)
RUN npm ci && npm run build

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose port for Render
EXPOSE 10000

# Create SQLite file if missing, migrate (and seed), then start Laravel
# DB_DATABASE must be set in Render env, e.g. /app/database/database.sqlite
CMD sh -c "mkdir -p \"$(dirname \"$DB_DATABASE\")\" \
 && [ -f \"$DB_DATABASE\" ] || touch \"$DB_DATABASE\" \
 && php artisan migrate --force \
 && php artisan db:seed --force || true \
 && php artisan serve --host=0.0.0.0 --port=10000"
