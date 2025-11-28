# File: `Dockerfile`
FROM php:8.4.15-cli

# Install system deps and PHP extensions commonly used by Laravel
# Dockerfile
RUN apt-get update -y && apt-get install -y \
    git unzip curl zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libicu-dev libxml2-dev zlib1g-dev libwebp-dev pkg-config \
    --no-install-recommends \
 && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) pdo_mysql mbstring bcmath gd xml zip intl \
 && rm -rf /var/lib/apt/lists/*

# Install Composer 2.9.2
RUN curl -sSL https://getcomposer.org/download/2.9.2/composer.phar -o /usr/local/bin/composer \
  && chmod +x /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first (layer caching)
COPY composer.json composer.lock* ./

# Create a non-root user matching common container user
RUN useradd -G www-data,root -u 1000 -m developer

# Default container behavior: ensure dependencies and run Laravel dev server
COPY . .

# Expose Laravel default dev port
EXPOSE 8000

# Entrypoint: install composer deps if missing, then serve
CMD [ "sh", "-lc", "\
  if [ ! -d vendor ]; then composer install --no-interaction --prefer-dist --optimize-autoloader; fi && \
  if [ ! -f .env ]; then cp .env.example .env; fi && \
  php artisan key:generate --force 2>/dev/null || true && \
  php artisan serve --host=0.0.0.0 --port=8000" ]
