FROM php:7.4-apache

# Instala as dependências necessárias e habilita as extensões do PHP
RUN set -ex; \
    apt update; \
    apt install -y \
        apt-transport-https \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libpng-dev \
        libxml2-dev \
        libonig-dev \
        libzip-dev \
        zip \
        libcurl4-openssl-dev; \
    docker-php-ext-configure gd --with-jpeg --with-freetype; \
    docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        mbstring \
        pdo_mysql \
        zip; \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

# Cria as pastas necessárias
RUN set -aux; \
    mkdir -p /var/www/html/image/cache/; \
    mkdir -p /var/www/html/image/catalog/; \
    mkdir -p /var/www/html/system/storage/cache/; \
    mkdir -p /var/www/html/system/storage/logs/; \
    mkdir -p /var/www/html/system/storage/download/; \
    mkdir -p /var/www/html/system/storage/upload/; \
    mkdir -p /var/www/html/system/storage/session/; \
    mkdir -p /var/www/html/system/storage/modification/; \
    [ ! -f /var/www/html/config.php ] && touch /var/www/html/config.php; \
    [ ! -f /var/www/html/admin/config.php ] && touch /var/www/html/admin/config.php; \
    \
    chown -R www-data: .

# Configura regras de exibição do log
RUN { \
        echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
        echo 'display_errors = Off'; \
        echo 'display_startup_errors = Off'; \
        echo 'error_log = /dev/stderr'; \
        echo 'log_errors = On'; \
        echo 'log_errors_max_len = 1024'; \
        echo 'ignore_repeated_errors = On'; \
        echo 'ignore_repeated_source = Off'; \
        echo 'html_errors = Off'; \
        echo 'zend.exception_ignore_args = Off'; \
        echo 'zend.exception_string_param_max_len = 25'; \
    } > /usr/local/etc/php/conf.d/error-logging.ini

# Configura o PHP para funcionamento da plataforma
RUN { \
        echo 'allow_url_fopen = On'; \
        echo 'default_charset = UTF-8'; \
        echo 'file_uploads = On'; \
        echo 'max_execution_time = 360'; \
        echo 'memory_limit = 128M'; \
        echo 'upload_max_filesize = 200M'; \
        echo 'open_basedir = none'; \
        echo 'user_agent = "Curl-OpenCartBrasil/1.5.0"'; \
        echo 'post_max_size = 200M'; \
        echo 'session.auto_start = Off'; \
        echo 'session.use_only_cookies = On'; \
        echo 'session.use_cookies = On'; \
        echo 'session.use_trans_sid = Off'; \
        echo 'session.cookie_httponly = Off'; \
        echo 'session.cache_limiter = nocache'; \
        echo 'session.gc_maxlifetime = 1'; \
        echo 'session.gc_divisor = 100'; \
    } > /usr/local/etc/php/conf.d/opencart-brasil.ini;

RUN set -aux; \
    { \
        echo "<VirtualHost *:80>"; \
        echo "    ServerAdmin webmaster@localhost"; \
        echo "    DocumentRoot /var/www/html"; \
        echo ""; \
        echo "    ErrorLog /dev/stderr"; \
        echo "    CustomLog /dev/stdout combined"; \
        echo "</VirtualHost>"; \
    } > /etc/apache2/sites-available/000-default.conf; \
    \
    if [ ! -f .htaccess ]; then \
        mv .htaccess.txt .htaccess; \
    fi; \
    \
    a2enmod rewrite;

EXPOSE 80

VOLUME /var/www/html

CMD ["apache2-foreground"]