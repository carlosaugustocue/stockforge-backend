# Usamos PHP 8.2 con Apache como base
FROM php:8.2-apache

# Instalamos dependencias del sistema y extensiones de PHP necesarias para Laravel y PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql gd

# Habilitamos el módulo de reescritura de Apache (vital para las rutas de Laravel)
RUN a2enmod rewrite

# Configuramos el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiamos los archivos del proyecto al contenedor
COPY . .

# Cambiamos los permisos de las carpetas de almacenamiento de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Exponemos el puerto 80 (Apache)
EXPOSE 80
