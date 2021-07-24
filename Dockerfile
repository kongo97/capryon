FROM php:8.0-apache

RUN a2enmod rewrite
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get update && apt-get install -y zip unzip vim libpq-dev && docker-php-ext-install pdo pdo_pgsql
