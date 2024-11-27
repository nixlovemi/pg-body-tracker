FROM php:8.0.28-apache

RUN apt-get update

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# install GD
RUN apt-get install -y \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev libxpm-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd \
      --with-jpeg=/usr/include/ \
      --with-freetype=/usr/include/

RUN docker-php-ext-install gd
# ==========

# mod rewrite
RUN a2enmod rewrite
ADD . /var/www/html