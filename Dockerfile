FROM php:8.2-apache

RUN docker-php-ext-install mysqli

RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2enmod mpm_prefork

COPY . /var/www/html/

CMD ["apache2-foreground"]