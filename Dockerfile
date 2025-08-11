FROM php:8.2-fpm-alpine

# استفاده از آروان کلود به‌عنوان mirror
RUN echo "https://mirror.arvancloud.ir/alpine/v3.18/main" > /etc/apk/repositories && \
    echo "https://mirror.arvancloud.ir/alpine/v3.18/community" >> /etc/apk/repositories && \
    apk update

# نصب پیش‌نیازهای لازم برای اکستنشن‌ها
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
    zip

# پیکربندی gd با مسیر مشخص
RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/freetype2 \
    --with-jpeg=/usr/include

# نصب اکستنشن‌ها
# نصب اکستنشن‌های ساده‌تر
RUN docker-php-ext-install pdo_sqlite
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install xml
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install session
RUN docker-php-ext-install dom
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-install intl


# نصب composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# مسیر پروژه
WORKDIR /var/www/html

# کپی پروژه
COPY . .

# نصب پکیج‌های php
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

# تنظیم دسترسی‌ها
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
