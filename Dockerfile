FROM php:8.2-apache

# Install dependencies and PDO MySQL extension
RUN apt-get update && apt-get install -y \
    libonig-dev \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

# Copy application files and SSL certificate
COPY . /var/www/html
COPY ssl/ca.pem /var/www/html/ssl/ca.pem

# Set permissions for logs, rate_limit, and ssl directories
RUN mkdir -p /var/www/html/logs /var/www/html/logs/rate_limit /var/www/html/ssl \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache
RUN echo '<Directory "/var/www/html">\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\nDirectoryIndex index.php' > /etc/apache2/conf-available/judge_system.conf \
    && a2enconf judge_system

# Expose port 80
EXPOSE 80