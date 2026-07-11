FROM php:8.3-cli

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    ca-certificates \
    openssl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        zip \
        pdo \
        pdo_mysql \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generate APP_KEY during build
RUN php artisan key:generate --force --no-interaction

# Install frontend dependencies and build assets
RUN npm install
RUN npm run build

# Laravel writable folders
RUN chmod -R 775 storage bootstrap/cache

# Render uses port 10000
EXPOSE 10000

# Start Laravel - clear stale config, then serve
CMD php artisan config:clear && php artisan serve --host=0.0.0.0 --port=10000
