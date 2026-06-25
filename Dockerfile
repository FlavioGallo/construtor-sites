FROM php:8.2-apache

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_mysql

# Desabilitar MPMs conflitantes e habilitar apenas um
RUN a2dismod mpm_event mpm_prefork mpm_worker 2>/dev/null || true
RUN a2enmod rewrite mpm_prefork

# Copiar arquivos do projeto
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html/

# Expor porta
EXPOSE 80
