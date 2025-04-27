FROM php:8.0-apache

WORKDIR /var/www/html
COPY src/ /var/www/html/

# Установка необходимых расширений PHP
RUN docker-php-ext-install pdo pdo_mysql

# Включаем mod_rewrite для Apache
RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html