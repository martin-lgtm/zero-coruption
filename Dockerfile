FROM php:8.2-cli

# OS deps + Node + headers for PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential autoconf pkg-config \
    git zip unzip npm \
    libzip-dev zlib1g-dev \
    sqlite3 libsqlite3-dev \
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Vite build
RUN npm ci && npm run build

# Permissions
RUN mkdir -p database && chmod -R 775 storage bootstrap/cache database

EXPOSE 10000

# Create SQLite if missing, migrate + seed, then start
CMD sh -c "mkdir -p \"$(dirname \"$DB_DATABASE\")\" \
 && [ -f \"$DB_DATABASE\" ] || touch \"$DB_DATABASE\" \
 && php artisan migrate --force \
 && php artisan db:seed --force || true \
 && php artisan serve --host=0.0.0.0 --port=10000"
