FROM richarvey/nginx-php-fpm:3.1.6

USER root

RUN apk add --no-cache \
    wkhtmltopdf \
    xvfb \
    ttf-dejavu \
    ttf-freefont \
    fontconfig && \
    rm -rf /var/cache/apk/*

# Increase PHP memory limit
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/memory-limit.ini

COPY . .

# Add Permssion Commands
RUN chmod -R 775 /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/storage

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
