FROM php:8.3-apache

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    APP_ENV=production

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        unzip \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        mbstring \
        xml \
        opcache \
    && a2enmod rewrite headers deflate expires \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/performance.conf /etc/apache2/conf-available/performance.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN a2enconf performance \
    && sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions

WORKDIR /var/www/html

COPY docker/php/composer.json docker/php/composer.lock ./
RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-scripts \
        --no-autoloader \
        --no-progress \
        --ignore-platform-reqs \
    && rm -rf /root/.composer/cache

COPY docker/php/ .
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/logs storage/uploads storage/exports \
    && cp -a vendor /opt/vendor \
    && md5sum composer.lock | awk '{print $1}' > /opt/vendor.stamp \
    && chown -R www-data:www-data /var/www/html /opt/vendor

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
