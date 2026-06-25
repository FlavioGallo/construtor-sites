FROM php:8.2-apache

# Instalar extensões do PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite do Apache (para o .htaccess funcionar)
RUN a2enmod rewrite

# Copiar arquivos do projeto
COPY . /var/www/html/

# Definir permissões
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
