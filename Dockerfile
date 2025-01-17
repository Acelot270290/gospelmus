# Use a imagem base do PHP 8.2 com PHP-FPM
FROM php:8.2-fpm

# Instalar pacotes e extensões necessárias
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instalar a extensão Redis
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Expor a porta do PHP-FPM
EXPOSE 9000

# Comando padrão para rodar o PHP-FPM
CMD ["php-fpm"]
