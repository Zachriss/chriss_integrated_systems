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

# Copy Laravel project (including storage/certs/aiven-ca.pem)
COPY . .

# Remove local .env - Render injects its own env vars at runtime.
# Leaving .env in the image can cause Dotenv to override Render's env vars.
RUN rm -f .env

# Install PHP dependencies (no dev, optimize autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install frontend dependencies and build assets
RUN npm install && npm run build

# Set Laravel writable folders
RUN chmod -R 775 storage bootstrap/cache

# Render uses port 10000
EXPOSE 10000

# Start Laravel - environment variables are injected by Render at runtime.
# optimize:clear clears all caches (config, route, view, event, etc.)
CMD php artisan optimize:clear && \
    php artisan serve --host=0.0.0.0 --port=10000
