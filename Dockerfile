FROM php:8.2-apache

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql

# Desabilitar TODOS os MPMs primeiro (isso resolve o erro!)
RUN a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true

# Habilitar APENAS um MPM
RUN a2enmod mpm_prefork

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar Apache para porta 8080
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf
RUN sed -i 's/:80>/:8080>/g' /etc/apache2/sites-available/000-default.conf

# Copiar arquivos
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 8080

CMD ["apache2-foreground"]
