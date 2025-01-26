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
    curl \
    mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip \
    && docker-php-ext-enable gd pdo_mysql mysqli zip

# Enable Apache modules for CodeIgniter and static files
RUN a2enmod rewrite headers

# Configure Apache to allow .htaccess overrides for CodeIgniter and static files
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/codeigniter.conf \
    && a2enconf codeigniter

# Set up .htaccess for handling static files and CodeIgniter routing
RUN echo '<IfModule mod_rewrite.c>\n\
    RewriteEngine On\n\
    # Allow direct access to existing files and directories\n\
    RewriteCond %{REQUEST_FILENAME} -f [OR]\n\
    RewriteCond %{REQUEST_FILENAME} -d\n\
    RewriteRule ^(.*)$ - [L]\n\
    # Pass all other requests to CodeIgniter index.php\n\
    RewriteRule ^(.*)$ index.php [L]\n\
</IfModule>' > /var/www/html/.htaccess

# Set permissions for the application and static files
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/uploads

# Set the working directory
WORKDIR /var/www/html

# Copy application files to the container
COPY . /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
