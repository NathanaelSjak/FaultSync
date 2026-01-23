FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk update && apk add --no-cache \
    git \
    curl \
    unzip \
    zip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    openssl-dev \
    linux-headers \
    nodejs \
    npm \
    $PHPIZE_DEPS

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    intl \
    zip \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY composer.json composer.lock ./
COPY package.json ./

RUN composer install --no-interaction --no-scripts

RUN npm install

COPY . /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache /var/www/html
RUN chmod -R 775 storage bootstrap/cache

COPY entrypoint/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT [ "/entrypoint.sh" ]