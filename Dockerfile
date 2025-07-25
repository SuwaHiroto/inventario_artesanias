# Usa una imagen base oficial de PHP con Apache (versión 8.2 es un buen punto de partida)
FROM php:8.2-apache

# Instala dependencias del sistema y extensiones de PHP.
# 'build-essential' y las librerías '-dev' son para compilar las extensiones.
# 'libjpeg62-turbo-dev' es el nombre correcto del paquete para JPEG en Debian/Bookworm.
# También instalamos la extensión 'zip' que es muy útil.
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype-dev \
    libzip-dev \
    unzip \
    libmysqlclient-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copia todos los archivos de tu aplicación al directorio raíz del servidor web de Apache.
COPY . /var/www/html/

# Configura los permisos para el usuario del servidor web (www-data)
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Expone el puerto 80, que es el puerto HTTP estándar.
EXPOSE 80
