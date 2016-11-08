FROM php:5.6-apache

MAINTAINER Andreas Ek <andreas@aekab.se>

RUN a2enmod rewrite

RUN pecl install xdebug-beta

RUN docker-php-ext-install mysql mysqli pdo pdo_mysql

RUN docker-php-ext-enable xdebug

ADD config/docker.conf /etc/apache2/sites-enabled/

ADD config/php.ini /usr/local/etc/php/
