# Use an official PHP image with Apache
FROM php:7.4-apache

# Set environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Install required extensions and dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql zip && \
    docker-php-ext-enable gd pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy the application files to the container
COPY . /var/www/html

# Set permissions for the application files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache for CodeIgniter (optional, adjust if necessary)
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/codeigniter.conf && \
    a2enconf codeigniter

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
