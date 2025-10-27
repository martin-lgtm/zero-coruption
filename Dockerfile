# ✅ Base image: PHP 8.2 (Debian Bookworm)
FROM php:8.2-cli-bookworm

# ✅ System deps for PHP extensions + SQLite + build tools
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev zlib1g-dev libpng-dev libxml2-dev libonig-dev \
    sqlite3 libsqlite3-dev build-essential pkg-config autoconf \
 && rm -rf /var/lib/apt/lists/*

# ✅ Install Node 20 LTS for Vite / Inertia
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs \
 && npm install -g npm@latest

# ✅ Install required PHP extensions
RUN docker-php-ext-configure zip \
 && docker-php-ext-install pdo pdo_sqlite mbstring zip bcmath

# ✅ Set working directory
WORKDIR /app

# ✅ Copy project files
COPY . /app

# ✅ Install Composer (latest stable)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ✅ Install PHP dependencies (prod only)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ✅ Build frontend (Vite)
RUN npm ci && npm run build

# ✅ Fix permissions
RUN mkdir -p storage bootstrap/cache /var/data \
 && chmod -R 775 storage bootstrap/cache /var/data

# ✅ Expose Render port
EXPOSE 10000

# ✅ CMD – safe mode (no reseeding)
CMD sh -c '\
  php artisan package:discover --ansi && \
  mkdir -p "$(dirname "$DB_DATABASE")" && \
  if [ ! -f "$DB_DATABASE" ]; then \
      echo "⚠️  Database file not found at $DB_DATABASE"; \
      echo "   (No automatic migrations or seeding will be run.)"; \
      touch "$DB_DATABASE"; \
  else \
      echo "✅ Using existing database: $DB_DATABASE"; \
  fi && \
  php artisan serve --host=0.0.0.0 --port=10000'
