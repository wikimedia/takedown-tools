FROM 'php:7.1-apache'

RUN a2enmod rewrite

RUN apt-get update \
	&& apt-get install libicu-dev --yes \
	&& rm -r /var/lib/apt/lists/*

RUN docker-php-ext-install intl pdo pdo_mysql opcache

RUN pecl channel-update pecl.php.net \
	&& pecl install apcu
