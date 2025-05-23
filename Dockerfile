FROM php:8.2-apache
RUN apt-get update && apt-get install -y \
    libonig-dev \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite
COPY . /var/www/html
RUN mkdir -p /var/www/html/logs /var/www/html/logs/rate_limit \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && echo '<Directory "/var/www/html">\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\nDirectoryIndex index.php' > /etc/apache2/conf-available/judge_system.conf \
    && a2enconf judge_system
EXPOSE 80