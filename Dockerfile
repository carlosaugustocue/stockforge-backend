FROM php:8.3-cli

# Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel/MySQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer para manejar dependencias de PHP
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Exponer el puerto 80 (que el docker-compose mapea al 8000)
EXPOSE 80

# Comando de inicio: instalar dependencias y levantar el servidor de Laravel
CMD bash -c "composer install && php artisan serve --host=0.0.0.0 --port=80"
