FROM richarvey/nginx-php-fpm:3.1.6

USER root

# Increase PHP memory limit
RUN echo "memory_limit = 768M" > /usr/local/etc/php/conf.d/memory-limit.ini

COPY . .

# Install Composer dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction || { echo "Composer install failed"; exit 1; }

# Set storage and cache permissions
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]