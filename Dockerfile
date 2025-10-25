# ✅ Use PHP 8.2 (Debian bullseye for compatibility)
FROM php:8.2-cli-bullseye

# ✅ Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev zlib1g-dev libpng-dev libxml2-dev libonig-dev \
    sqlite3 libsqlite3-dev build-essential pkg-config autoconf

# ✅ Install Node.js 18 (for Vite / Inertia)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
 && apt-get install -y nodejs \
 && npm install -g npm@latest

# ✅ Configure & install PHP extensions
RUN docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip bcmath \
 && rm -rf /var/lib/apt/lists/*

# ✅ Set working directory
WORKDIR /app

# ✅ Copy project files
COPY . /app

# ✅ Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ✅ Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ✅ Build Vue frontend (Vite)
RUN npm ci && npm run build

# ✅ Set proper permissions for Laravel
RUN mkdir -p database && chmod -R 775 storage bootstrap/cache database

# ✅ Expose port for Render
EXPOSE 10000

# ✅ Start the Laravel app:
# - Discover packages
# - Create SQLite file if missing
# - Run migrations + seed
# - Serve Laravel app
CMD sh -c 'php artisan package:discover --ansi \
 && mkdir -p "$(dirname "$DB_DATABASE")" \
 && [ -f "$DB_DATABASE" ] || touch "$DB_DATABASE" \
 && php artisan migrate --force \
 && php artisan db:seed --force || true \
 && php artisan serve --host=0.0.0.0 --port=10000'
