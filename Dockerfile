FROM php:7.2.19-fpm as php
LABEL maintainer="Hamdi Fourati <hamdi.fourati@oyez.fr>" \
      app="symfony" \
      service="tcp:9000"

WORKDIR /app
ENV SYMFONY_ENV "dev"

# Set timezone
ENV TIMEZONE "UTC"
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone && \
    printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini

# Install ICU 62.1: see http://download.icu-project.org/files/icu4c/63.1/icu4c-63_1-Ubuntu-18.04-x64.tgz
RUN curl -s https://ayera.dl.sourceforge.net/project/icu/ICU4C/63.1/icu4c-63_1-Ubuntu-18.04-x64.tgz --output /tmp/icu4.tgz && \
    tar xaf /tmp/icu4.tgz -C /tmp && \
    cp /tmp/icu/usr / -r && \
    rm /tmp/icu* -r

# Install PHP extensions
RUN apt -q update && apt -q install -y zlib1g-dev libjpeg-dev libpng-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-jpeg-dir --with-png-dir --with-zlib-dir --with-freetype-dir && \
    docker-php-ext-install -j$(nproc) zip gd intl opcache pdo pdo_mysql mbstring && \
    pecl install opencensus-0.1.0

COPY docker/php.ini /usr/local/etc/php/php.ini
# Browscap: http://browscap.org/stream?q=PHP_BrowsCapINI
COPY docker/php_browscap.ini /usr/local/etc/php/php_browscap.ini

COPY --chown=www-data:www-data . .

RUN php bin/symfony_requirements

FROM nginx as nginx
LABEL app="nginx+fastcgi" \
      service="tcp:80"

WORKDIR /app

COPY docker/api.conf /etc/nginx/conf.d/api.conf

COPY web web
