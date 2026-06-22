FROM php:8.4-cli-bookworm AS vendor

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        dom \
        fileinfo \
        gd \
        intl \
        mbstring \
        xml \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts \
    --optimize-autoloader


FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build


FROM php:8.4-fpm-bookworm AS app

ENV APP_ENV=production \
    APP_DEBUG=false \
    COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libjpeg62-turbo-dev \
        libfcgi-bin \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        dom \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        xml \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN mkdir -p /opt/app-defaults/thumbnails \
    && if [ -d public/images/thumbnail ]; then cp -a public/images/thumbnail/. /opt/app-defaults/thumbnails/ 2>/dev/null || true; fi
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY docker/php/conf.d/production.ini /usr/local/etc/php/conf.d/production.ini
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/images/thumbnail \
    && chown -R www-data:www-data storage bootstrap/cache public/images/thumbnail

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm", "-F"]


FROM nginx:1.27-alpine AS nginx

WORKDIR /var/www/html

COPY --from=app /var/www/html/public ./public
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www/html/storage/app/public \
    && ln -s /var/www/html/storage/app/public /var/www/html/public/storage
