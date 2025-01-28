# Use an official PHP image with Apache
FROM php:7.4-apache

# Set environment variables for Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Update package manager and install required dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    mariadb-client \
    curl \
    libxml2-dev \  # Required for SOAP
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip soap \  # Install SOAP
    && docker-php-ext-enable gd pdo_mysql mysqli zip soap  # Enable SOAP

# Enable Apache mod_rewrite for CodeIgniter
RUN a2enmod rewrite

# Configure Apache to allow .htaccess overrides
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy application files to the container
COPY . /var/www/html

# Set permissions for writable directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/uploads /var/www/html/temp

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
