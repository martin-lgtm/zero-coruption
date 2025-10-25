# ✅ Use official PHP 8.2 CLI image
FROM php:8.2-cli

# ✅ Install system dependencies, Node, and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip npm curl \
    libzip-dev zlib1g-dev libpng-dev libonig-dev libxml2-dev \
    sqlite3 libsqlite3-dev build-essential pkg-config autoconf \
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip bcmath \
 && rm -rf /var/lib/apt/lists/*

# ✅ Set working directory
WORKDIR /app

# ✅ Copy project files
COPY . /app

# ✅ Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ✅ Install PHP dependencies (skip scripts during build)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ✅ Build frontend (Vite / Vue)
RUN npm ci && npm run build

# ✅ Set permissions
RUN mkdir -p database && chmod -R 775 storage bootstrap/cache database

# ✅ Expose port 10000 for Render
EXPOSE 10000

# ✅ Start container: finalize Composer, create SQLite DB, migrate, seed, then serve
CMD sh -c 'php artisan package:discover --ansi \
 && mkdir -p $(dirname "$DB_DATABASE") \
 && [ -f "$DB_DATABASE" ] || touch "$DB_DATABASE" \
 && php artisan migrate --force \
 && php artisan db:seed --force || true \
 && php artisan serve --host=0.0.0.0 --port=10000'
