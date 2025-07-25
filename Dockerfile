# Usa una imagen base oficial de PHP con Apache (versión 8.2 es un buen punto de partida)
FROM php:8.2-apache

# Instala las dependencias del sistema necesarias y luego las extensiones de PHP.
# 'zip' y 'unzip' son utilidades comunes.
# 'libpng-dev', 'libjpeg-dev', 'libfreetype-dev' son dependencias para la extensión 'gd'.
# 'libmysqlclient-dev' es la dependencia para la extensión de MySQL.
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    libmysqlclient-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Copia todos los archivos de tu aplicación al directorio raíz del servidor web de Apache.
# '/var/www/html/' es el directorio por defecto donde Apache sirve los archivos.
COPY . /var/www/html/

# Configura los permisos para el usuario del servidor web (www-data)
# Esto asegura que Apache pueda leer tus archivos y escribir si es necesario (ej. logs, uploads).
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Si tu aplicación tiene una ruta base diferente o quieres configuraciones específicas de Apache,
# puedes añadir un archivo de configuración de Apache personalizado.
# Por ejemplo, si tu index.php no está en la raíz, o si necesitas reglas de reescritura.
# Primero, asegúrate de tener un archivo '000-default.conf' en la raíz de tu proyecto.
# COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
# RUN a2enmod rewrite && a2ensite 000-default.conf && service apache2 reload

# Expone el puerto 80, que es el puerto HTTP estándar y el que Apache escucha por defecto.
EXPOSE 80

# El comando CMD ya está definido en la imagen base de php-apache para iniciar Apache.
# No necesitas un CMD aquí a menos que quieras anular el comportamiento por defecto.
