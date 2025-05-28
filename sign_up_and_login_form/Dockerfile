# Use official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy contents of public folder to web root
COPY public/ /var/www/html/

# Optional: Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose default Apache port
EXPOSE 80
