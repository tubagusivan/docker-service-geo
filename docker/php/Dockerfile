FROM php:7.2-fpm

RUN apt-get update && docker-php-ext-install pdo_mysql

# Copy the php config file
COPY ./docker/php/php-fpm.conf /etc/php/7.1/fpm/pool.d/www.conf

# Copy the application code
COPY . /code

VOLUME ["/code"]
