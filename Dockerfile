# ✅ Use official PHP 8.2 CLI image (Debian Bookworm)
FROM php:8.2-cli

# ✅ System deps + Node/npm + libs for PHP extensions (NO libonig-dev)
RUN apt-get update && apt-get install -y \
    git unzip zip curl npm \
    libzip-dev zlib1g-dev libpng-dev libxml2-dev \
    sqlite3 libsqlite3-dev build-essential pkg-config autoconf \
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip bcmath \
 && rm -rf /var/lib/apt/lists/*

# ✅ Working dir
WORKDIR /app

# ✅ Copy project
COPY . /app

# ✅ Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ✅ PHP deps (skip scripts during image build)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ✅ Build frontend (Vite / Vue)
RUN npm ci && npm run build

# ✅ Permissions
RUN mkdir -p database && chmod -R 775 storage bootstrap/cache database

# ✅ Expose Render port
EXPOSE 10000

# ✅ On container start: finalize Composer, ensure SQLite exists, migrate/seed, serve
# Make sure DB_DATABASE is set in Render env, e.g. /app/database/database.sqlite
CMD sh -c 'php artisan package:discover --ansi \
 && mkdir -p "$(dirname "$DB_DATABASE")" \
 && [ -f "$DB_DATABASE" ] || touch "$DB_DATABASE" \
 && php artisan migrate --force \
 && php artisan db:seed --force || true \
 && php artisan serve --host=0.0.0.0 --port=10000'
