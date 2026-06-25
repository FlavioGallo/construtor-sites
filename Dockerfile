FROM php:8.2-fpm

# Instalar Nginx
RUN apt-get update && apt-get install -y nginx

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql

# Copiar arquivos do projeto
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html/

# Copiar configuração do Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Expor porta 8080
EXPOSE 8080

# Iniciar PHP-FPM e Nginx
CMD service php8.2-fpm start && nginx -g 'daemon off;'
