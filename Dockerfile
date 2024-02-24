FROM php:8.2-fpm

ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_ENABLE_CLI=0
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1
ENV PHP_OPCACHE_REVALIDATE_FREQ=1

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \ 
    nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets opcache

# Copy composer executable.
COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

# Copy configuration files.
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install redis
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis


# Set working directory to /var/www.
WORKDIR /var/www

# Adjust user permission & group
RUN usermod --uid 1000 www-data
RUN groupmod --gid 1001 www-data

# Copy files from current folder to container current folder (set in workdir).
COPY --chown=www-data:www-data . .

# Create laravel caching folders.
RUN mkdir -p /var/www/storage/framework /var/www/storage/framework/cache \
    /var/www/storage/framework/testing /var/www/storage/framework/sessions \
    /var/www/storage/framework/views

RUN mkdir -p /var/www/storage /var/www/storage/logs /var/www/storage/framework \
    /var/www/storage/framework/sessions /var/www/bootstrap

# Fix files ownership.
RUN chown -R www-data:www-data /var/www/storage
RUN chown -R www-data:www-data /var/www/storage/framework
RUN chown -R www-data:www-data /var/www/storage/framework/sessions
RUN chown -R www-data:www-data /var/www/storage/logs

# Set correct permission.
RUN chmod -R 755 /var/www/storage
RUN chmod -R 755 /var/www/storage/logs
RUN chmod -R 755 /var/www/storage/framework
RUN chmod -R 755 /var/www/storage/framework/sessions
RUN chmod -R 755 /var/www/bootstrap

# Run the entrypoint file.
ENTRYPOINT [ "docker/entrypoint.sh" ]