FROM php:7.4-apache
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN pecl install xdebug-2.9.5 && docker-php-ext-enable xdebug
RUN echo "xdebug.remote_enable=1\nxdebug.remote_mode=req\nxdebug.remote_host=host.docker.internal\nxdebug.remote_port=9000\n" >>  /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN apt-get update && apt-get install -y unzip curl && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
ADD install-composer.sh /tmp/install-composer.sh
RUN sh -e /tmp/install-composer.sh && rm /tmp/install-composer.sh
RUN a2enmod rewrite
ADD 000-default.conf /etc/apache2/sites-available/000-default.conf
