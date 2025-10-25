FROM php:8.2-cli

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip npm curl \
    libzip-dev zlib1g-dev libpng-dev libonig-dev libxml2-dev sqlite3 libsqlite3-dev \
    build-essential pkg-config autoconf \
 && docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip \
 && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Copy project files
COPY . /app

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies (without dev)
RUN composer install --no-dev --optimize-autoloader

# Build frontend (Vite)
RUN npm ci && npm run build

# Ensure writable dirs
RUN mkdir -p database && chmod -R 775 storage bootstrap/cache database

# Expose port for Render
EXPOSE 10000

# Run migrations automatically and start server
CMD sh -c "mkdir -p \"$(dirname \"$DB_DATABASE\")\" \
 && [ -f \"$DB_DATABASE\" ] || touch \"$DB_DATABASE\" \
 && php artisan migrate --force \
 && php artisan db:seed --force || true \
 && php artisan serve --host=0.0.0.0 --port=10000"
