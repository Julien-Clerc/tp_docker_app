# Étape de construction
FROM php:8.2-cli AS builder

# Installation des dépendances
RUN apt-get update \
    && apt-get install -y \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définition du répertoire de travail
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

# Copie des fichiers de l'application et installation des dépendances
COPY . .
RUN composer install

# Étape de production
FROM php:8.2-apache

# Copie des fichiers de l'étape de construction
COPY --from=builder /app /var/www/html


# Exposition du port
EXPOSE 80

# Commande de démarrage d'Apache
CMD ["apache2-foreground"]