# ✅ PHP 8.2 on Debian bullseye (has libonig-dev for mbstring)
FROM php:8.2-cli-bookworm

# ✅ System deps for PHP extensions + SQLite
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev zlib1g-dev libpng-dev libxml2-dev libonig-dev \
    sqlite3 libsqlite3-dev build-essential pkg-config autoconf

# ✅ Install Node 20 LTS (needed by Vite / Inertia)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs

# ✅ PHP extensions
RUN docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip bcmath \
 && rm -rf /var/lib/apt/lists/*

# ✅ Set working directory
WORKDIR /app

# ✅ Copy project files
COPY . /app

# ✅ Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ✅ Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ✅ Build frontend (Vite)
RUN npm ci && npm run build

# ✅ Fix permissions
RUN mkdir -p database && chmod -R 775 storage bootstrap/cache database

# ✅ Expose Render port
EXPOSE 10000

# ✅ Auto-create SQLite DB, migrate & seed, then serve
CMD sh -c 'php artisan package:discover --ansi \
 && mkdir -p "$(dirname "$DB_DATABASE")" \
 && [ -f "$DB_DATABASE" ] || touch "$DB_DATABASE" \
 && php artisan migrate --force \
 && php artisan db:seed --force || true \
 && php artisan serve --host=0.0.0.0 --port=10000'
