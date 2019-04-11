# Service
FROM php:7.1-apache

RUN a2enmod rewrite env

# System Dependencies.
RUN apt-get update && apt-get install -y \
		libicu-dev \
		zip \
		unzip \
	--no-install-recommends && rm -r /var/lib/apt/lists/*

RUN set -ex \
	&& buildDeps=' \
		libsqlite3-dev \
		libzip-dev \
	' \
	&& apt-get update && apt-get install -y --no-install-recommends $buildDeps && rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure zip --with-libzip \
	&& docker-php-ext-install intl opcache pdo_mysql pdo_sqlite \
	&& apt-get purge -y --auto-remove $buildDeps

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=60'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# set the max upload
RUN { \
		echo 'post_max_size=64M'; \
		echo 'upload_max_filesize=64M'; \
	} > /usr/local/etc/php/conf.d/max-upload.ini

# Environment
ENV APP_ENV prod
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV JWT_PASSPHRASE 87155686a4a5dd1cfd04daf3ba1f5af8
ENV DATABASE_URL sqlite:////var/www/var/data/data.db

# Copy Composer
COPY --from=composer:1.8 /usr/bin/composer /usr/bin/composer

# Copy the app
COPY ./ /var/www

# Install Dependencies.
RUN composer --no-dev install -d /var/www

# Add the bin directory to the path.
ENV PATH="/var/www/bin:${PATH}"

# Touch the SQLite Database and set the permissions
RUN mkdir -p ../var/data \
	&& chmod 777 ../var/data \
	&& touch ../var/data/data.db \
	&& chown www-data:www-data ../var/data/data.db

# Generate a Key for JWT
RUN key-create

# Create the database schema and load the fixtures
RUN db-setup
