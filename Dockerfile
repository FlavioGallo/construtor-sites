FROM php:8.2-apache

# Instalar extensões PHP (já vem com Apache configurado!)
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite para o .htaccess funcionar
RUN a2enmod rewrite

# Configurar Apache para porta 8080 (Railway usa essa porta)
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf && \
    sed -i 's/:80>/:8080>/g' /etc/apache2/sites-available/000-default.conf

# Copiar arquivos do projeto
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 8080

CMD ["apache2-foreground"]
