# ==============================================================================
# DOCKERFILE - NAVASENA MANAGEMENT
# Optimized for Dokploy / Docker Production Deployment
# PHP 8.2 with Apache, MySQL PDO, GD (with FreeType & JPEG), Zip, Opcache
# ==============================================================================

FROM php:8.2-apache

# 1. Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Configure PHP Production Settings
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=25M'; \
    echo 'memory_limit=256M'; \
    echo 'date.timezone=Asia/Jakarta'; \
} > /usr/local/etc/php/conf.d/navasena-custom.ini

# 3. Configure Apache DocumentRoot to /var/www/html/public & Enable Rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && echo '<Directory /var/www/html/public>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/navasena.conf \
    && a2enconf navasena \
    && a2enmod rewrite headers

# 4. Set Working Directory
WORKDIR /var/www/html

# 5. Copy Application Source Code
COPY . /var/www/html/

# 6. Ensure Storage Permissions & Entrypoint Execution
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/exports \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage \
    && chmod +x /var/www/html/docker-entrypoint.sh

# 7. Expose Port 80
EXPOSE 80

# 8. Entrypoint & Default Command
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
