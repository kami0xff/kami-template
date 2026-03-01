# ==============================================
# Development Dockerfile
# Lightweight PHP CLI — uses Laravel's built-in server
# ==============================================

# ==============================================
# Stage 1: Base PHP with common extensions
# ==============================================
FROM php:8.3-cli-alpine AS base

RUN apk add --no-cache \
    postgresql-dev \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    oniguruma \
    icu-libs

# ==============================================
# Stage 2: Development image
# ==============================================
FROM base AS development

RUN apk add --no-cache --virtual .build-deps \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apk del .build-deps

WORKDIR /var/www/html

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
