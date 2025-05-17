FROM richarvey/nginx-php-fpm:3.1.6

USER root

# Install wkhtmltopdf and dependencies
RUN apk add --no-cache \
    xvfb \
    ttf-dejavu \
    ttf-freefont \
    fontconfig \
    libxrender \
    libxext \
    && wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox-0.12.6.1-3.alpine.3.16.x86_64.apk \
    && apk add --allow-untrusted wkhtmltox-0.12.6.1-3.alpine.3.16.x86_64.apk \
    && rm wkhtmltox-0.12.6.1-3.alpine.3.16.x86_64.apk \
    && rm -rf /var/cache/apk/* \
    && wkhtmltopdf --version

# Increase PHP memory limit
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/memory-limit.ini

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