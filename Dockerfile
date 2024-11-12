FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install necessary PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Clear cache and install dependencies
RUN composer clear-cache
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs
