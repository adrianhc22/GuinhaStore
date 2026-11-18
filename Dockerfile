# Imagen de PHP con Apache, MySQL (mysqli), imágenes (gd) y fuentes
FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpng-dev libjpeg-dev libwebp-dev fonts-dejavu-core \
    && docker-php-ext-install mysqli \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Más memoria para procesar fotos de productos
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini
