FROM php:8.2-apache

# Instalar extensões PHP necessárias
RUN apt-get update && apt-get install -y libzip-dev unzip && \
    docker-php-ext-install pdo pdo_mysql zip && \
    rm -rf /var/lib/apt/lists/*

# Desabilitar TODOS os MPMs primeiro, depois habilitar só um
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true
RUN a2enmod mpm_prefork rewrite

# Configurar Apache para rodar em porta 8080 (Railway usa essa porta)
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf && \
    sed -i 's/:80>/:8080>/g' /etc/apache2/sites-available/000-default.conf

# Copiar arquivos do projeto
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 8080
CMD ["apache2-foreground"]
