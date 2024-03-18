FROM php:8.2-fpm-alpine

# Install packages
RUN apk add --no-cache curl git build-base zlib-dev oniguruma-dev autoconf bash
RUN apk add --update linux-headers


RUN apk add --no-cache icu-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
# Xdebug
ARG INSTALL_XDEBUG=false
RUN if [ ${INSTALL_XDEBUG} = true ]; \
    then \
      pecl install xdebug && docker-php-ext-enable xdebug; \
    fi;

COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Postgres
RUN apk add --no-cache libpq-dev && docker-php-ext-install pdo_mysql

# Configure non-root user.
ARG PUID=1000
ARG PGID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# Nginx
RUN apk add --update --no-cache nginx
COPY ./docker/php/nginx.conf /etc/nginx/
RUN chown -Rf www-data:www-data /var/lib/nginx

# Supervisor
RUN apk add --no-cache supervisor
COPY ./docker/php/supervisord.conf /etc/supervisord.conf

# Source code
RUN chown www-data:www-data /var/www
COPY --chown=www-data:www-data ./ /var/www
WORKDIR /var/www

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#ARG BUILD_MODE=dev

#RUN if [ ${BUILD_MODE} = dev ]; \
#    then \
RUN export COMPOSER_ALLOW_SUPERUSER=1; \
    composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader; \
    chown -R www-data:www-data /var/www/vendor/;
#    else \
#      composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader; \
#    fi;

EXPOSE 8080

CMD ["/bin/sh", "./docker/php/run.sh"]