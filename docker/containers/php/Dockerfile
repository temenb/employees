FROM php:8.2-fpm

## Main
#RUN apt-get update && \
#    apt-get install --no-install-recommends -y \
#        imagemagick \
#        nano \
#        mc \
#        memcached \
#        sudo \
#        git \
#        zip \
#        unzip \
#        libicu-dev \
#        zlib1g-dev \
#        libzip-dev \
#        libxml2-dev && \
#        docker-php-ext-install zip mbstring intl pcntl exif iconv && \
#    rm -r /var/lib/apt/lists/*
#
## Mcrypt
#RUN apt-get update && \
#    apt-get install --no-install-recommends -y \
#        libmcrypt-dev && \
#        pecl install mcrypt-1.0.2 && \
#    rm -r /var/lib/apt/lists/*
#
## GD
#RUN apt-get update && \
#    apt-get install --no-install-recommends -y \
#        libpng-dev \
#        libjpeg-dev \
#        libfreetype6-dev && \
#        docker-php-ext-configure gd --with-png-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-freetype-dir=/usr/include/ && \
#        docker-php-ext-install gd && \
#    rm -r /var/lib/apt/lists/*

# Postgres
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# Supervisor
RUN apt-get update && \
    apt-get install --no-install-recommends -y \
        supervisor && \
    rm -r /var/lib/apt/lists/*
COPY ./config/laravel-worker.conf /etc/supervisor/conf.d

WORKDIR /project
