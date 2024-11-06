# Use the official PHP image with Apache
FROM php:8.1-apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y libpng-dev libjpeg62-turbo-dev libfreetype6-dev libzip-dev unzip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql zip gd

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files to the container
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-dev

# Run Laravel commands
RUN php artisan key:generate && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the port Apache is running on
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
