FROM php:8.2-apache

# FIX MPM ERROR
RUN sed -i 's/mpm_event/mpm_prefork/' /etc/apache2/mods-enabled/*.load || true \
 && a2dismod mpm_event mpm_worker || true \
 && a2enmod mpm_prefork

# kopjo projektin
COPY . /var/www/html/

# porti
EXPOSE 80