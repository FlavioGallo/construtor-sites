FROM php:8.2-fpm

# Instalar Nginx
RUN apt-get update && apt-get install -y nginx

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql

# Copiar arquivos
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# Copiar config do Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Expor porta
EXPOSE 8080

# Iniciar PHP-FPM e Nginx corretamente
CMD php-fpm & nginx -g 'daemon off;'
