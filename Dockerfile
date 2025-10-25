FROM php:8.2-cli-bullseye

RUN apt-get update && apt-get install -y \
    git unzip zip curl npm \
    libzip-dev zlib1g-dev libpng-dev libxml2-dev \
    sqlite3 libsqlite3-dev build-essential pkg-config autoconf \
    libonig-dev \
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip bcmath \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN npm ci && npm run build

RUN mkdir -p database && chmod -R 775 storage bootstrap/cache database

EXPOSE 10000
CMD sh -c 'php artisan package:discover --ansi \
 && mkdir -p "$(dirname "$DB_DATABASE")" \
 && [ -f "$DB_DATABASE" ] || touch "$DB_DATABASE" \
 && php artisan migrate --force \
 && php artisan db:seed --force || true \
 && php artisan serve --host=0.0.0.0 --port=10000'
