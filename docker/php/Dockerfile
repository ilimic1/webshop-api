FROM php:8.2-fpm

WORKDIR /var/www/laravel

# Copy php-extension-installer script for easier installation of PHP extensions

COPY --from=mlocati/php-extension-installer:2.2.19 /usr/bin/install-php-extensions /usr/local/bin/

# install the PHP extensions we need

RUN install-php-extensions pdo_pgsql
RUN install-php-extensions bcmath
RUN install-php-extensions xdebug
RUN install-php-extensions opcache
RUN install-php-extensions @composer-2

# add config overrides

COPY zz-php.ini $PHP_INI_DIR/conf.d/zz-php.ini
COPY zz-www.conf /usr/local/etc/php-fpm.d/zz-www.conf
COPY zz-xdebug.ini $PHP_INI_DIR/conf.d/zz-xdebug.ini
COPY zz-opcache.ini $PHP_INI_DIR/conf.d/zz-opcache.ini
