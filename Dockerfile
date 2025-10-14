# Dockerfile para CMS Visual - Optimizado para DigitalOcean
FROM php:8.1-apache

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# Configurar DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Actualizar configuración Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar código fuente
COPY . /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/
RUN chmod -R 777 /var/www/html/uploads/
RUN chmod 666 /var/www/html/data.json
RUN chmod 666 /var/www/html/mensajes.json

# Crear archivos de logs
RUN mkdir -p /var/log/apache2
RUN touch /var/log/apache2/error.log
RUN touch /var/log/apache2/access.log

# Configuración PHP optimizada para producción
RUN echo "upload_max_filesize = 50M" >> /usr/local/etc/php/php.ini-production && \
    echo "post_max_size = 50M" >> /usr/local/etc/php/php.ini-production && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini-production && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/php.ini-production && \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Configurar timezone
RUN echo "date.timezone = America/New_York" >> /usr/local/etc/php/php.ini

# Configuración de Apache para el CMS
RUN echo "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf

# Exponer puerto
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]