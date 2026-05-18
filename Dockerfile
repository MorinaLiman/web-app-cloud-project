FROM php:8.2-apache

# Disable conflicting MPMs + enable correct one
RUN a2dismod mpm_event mpm_worker || true \
 && a2enmod mpm_prefork || true

COPY . /var/www/html/

EXPOSE 80