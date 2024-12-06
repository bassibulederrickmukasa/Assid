# Use the official PHP image from Docker Hub
FROM php:8.0-apache

# Install dependencies
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install Composer (PHP dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy your project files into the container
COPY . /var/www/html

# Install PHP dependencies using Composer
RUN composer install

# Expose the web server port
EXPOSE 80

# Start the Apache server
CMD ["apache2-foreground"]
