FROM php:8.4.28-apache

# Installa il driver PDO per MySQL
RUN docker-php-ext-install pdo pdo_mysql

RUN mv "/usr/local/etc/php/php.ini-production" "/usr/local/etc/php/php.ini"
COPY . /var/www/html