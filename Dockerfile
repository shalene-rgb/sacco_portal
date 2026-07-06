FROM php:8.2-apache

# Install MySQL extension for PHP
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy your project files into the Apache web directory
COPY . /var/www/html/

# Expose port 80
EXPOSE 80