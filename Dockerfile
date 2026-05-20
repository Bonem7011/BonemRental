FROM php:8.2-apache
# Installation des dépendances PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev
# Installation des extensions PHP pour PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql
# Copie du code dans le conteneur
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html