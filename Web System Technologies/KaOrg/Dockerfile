# syntax=docker/dockerfile:1
FROM php:8.5-apache-bookworm AS app
RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libonig-dev libpq-dev libzip-dev unzip \
    && docker-php-ext-install -j"$(nproc)" intl mbstring pdo_pgsql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY . .
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci --include=dev --ignore-scripts
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
COPY --from=app /var/www/html/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build

FROM app AS production
COPY --from=assets /app/public/build ./public/build
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY render-entrypoint.sh /usr/local/bin/render-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/render-entrypoint \
    && chmod +x /usr/local/bin/render-entrypoint
ENV PORT=10000
EXPOSE 10000
ENTRYPOINT ["render-entrypoint"]
CMD ["apache2-foreground"]
