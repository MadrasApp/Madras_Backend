# Use PHP image from the specified registry with Apache
FROM registry.docker.ir/library/php:7.4-apache

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
    libxml2-dev \
    redis-server \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip soap \
    && docker-php-ext-enable gd pdo_mysql mysqli zip soap

# Enable Apache mod_rewrite for CodeIgniter
RUN a2enmod rewrite headers

# Configure Apache for large file uploads
RUN echo 'LimitRequestBody 1073741824\n\
Timeout 300\n\
ProxyTimeout 300' > /etc/apache2/conf-available/upload-limits.conf \
    && a2enconf upload-limits

# Configure Apache to allow .htaccess overrides
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

# Configure PHP for large file uploads
RUN echo 'upload_max_filesize = 1000M\n\
post_max_size = 1000M\n\
max_execution_time = 5000\n\
max_input_time = 5000\n\
memory_limit = 1000M\n\
max_file_uploads = 20\n\
file_uploads = On' > /usr/local/etc/php/conf.d/uploads.ini

# Add new configuration to serve /lexoya/var/www/html/uploads
RUN echo '<Directory /lexoya/var/www/html/uploads>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride None\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/lexoya.conf \
    && a2enconf lexoya

# Ensure the lexoya directory exists and set permissions
RUN mkdir -p /lexoya/var/www/html/uploads \
    && chown -R www-data:www-data /lexoya \
    && chmod -R 775 /lexoya

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Copy composer files first for better Docker cache
COPY composer.json composer.lock /var/www/html/

# Install Composer dependencies (including AWS SDK)
RUN composer install --no-dev --optimize-autoloader

# Set the working directory
WORKDIR /var/www/html

# Copy application files to the container
COPY . /var/www/html

# Set permissions for writable directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/uploads /var/www/html/temp \
    && chmod 777 /tmp \
    && chown www-data:www-data /tmp

# Expose ports 80 for Apache and 6379 for Redis
EXPOSE 80 6379

# Set default AWS environment variables (override in production)
ENV AWS_ACCESS_KEY_ID=BWDSOR9C0NBLRJ731D1P
ENV AWS_SECRET_ACCESS_KEY=d70GHwTERZ1BJ11hZCXfAuIFpuDjCm0Sniauy8Np
ENV AWS_REGION=default
ENV AWS_BUCKET=amoozim
ENV AWS_S3_ENDPOINT=https://s3.lexoya.com

# Create a health check script
RUN echo '#!/bin/bash\n\
echo "PHP Upload Configuration:"\n\
php -r "echo \"upload_max_filesize: \" . ini_get(\"upload_max_filesize\") . \"\\n\";"\n\
php -r "echo \"post_max_size: \" . ini_get(\"post_max_size\") . \"\\n\";"\n\
php -r "echo \"max_execution_time: \" . ini_get(\"max_execution_time\") . \"\\n\";"\n\
php -r "echo \"memory_limit: \" . ini_get(\"memory_limit\") . \"\\n\";"\n\
echo "Temp directory permissions:"\n\
ls -la /tmp\n\
echo "Uploads directory permissions:"\n\
ls -la /var/www/html/uploads' > /usr/local/bin/health-check.sh \
    && chmod +x /usr/local/bin/health-check.sh

# Start Apache in the foreground and Redis server
CMD service redis-server start && apache2-foreground
