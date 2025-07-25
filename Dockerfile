# Usa una imagen base oficial de PHP con Apache (versión 8.2 es un buen punto de partida)
FROM php:8.2-apache

# Instala extensiones de PHP necesarias
# 'pdo_pgsql' y 'pgsql' son cruciales para conectar PHP con PostgreSQL.
# 'gd' es común para manipulación de imágenes. Puedes añadir otras si las necesitas.
# 'libpq-dev' es necesario para compilar las extensiones de PostgreSQL.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/* \

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
# RUN a2ensite 000-default.conf && a2dissite 000-default.conf # Deshabilita el default y habilita el tuyo
# RUN service apache2 reload # Recarga Apache para aplicar los cambios

# Expone el puerto 80, que es el puerto HTTP estándar y el que Apache escucha por defecto.
EXPOSE 80

# El comando CMD ya está definido en la imagen base de php-apache para iniciar Apache.
# No necesitas un CMD aquí a menos que quieras anular el comportamiento por defecto.