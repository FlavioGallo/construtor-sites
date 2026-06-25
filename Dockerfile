FROM php:8.2-apache

# Instalar apenas extensões PHP
RUN docker-php-ext-install pdo pdo_mysql

# Configurar Apache para porta 8080 (Railway)
RUN echo "Listen 8080" > /etc/apache2/ports.conf && \
    echo '<VirtualHost *:8080>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /var/www/html' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /var/www/html>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Copiar todos os arquivos
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 8080

CMD ["apache2-foreground"]
