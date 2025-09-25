FROM php:8.4-fpm-alpine

RUN echo "https://mirror.arvancloud.ir/alpine/v3.21/main" > /etc/apk/repositories && \
    echo "https://mirror.arvancloud.ir/alpine/v3.21/community" >> /etc/apk/repositories && \
    apk update

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zlib-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite-dev \
    libxml2-dev \
    autoconf \
    g++ \
    make \
    pkgconfig \
    libtool \
    bash \
    curl \
    git \
    unzip \
    zip \
    icu-dev \
    linux-headers


RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-configure intl

RUN docker-php-ext-install pdo_sqlite
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install xml
RUN docker-php-ext-install dom
RUN docker-php-ext-install zip
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install ctype
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install session
RUN docker-php-ext-install gd
RUN docker-php-ext-install intl


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true

EXPOSE 9000

CMD ["php-fpm"]