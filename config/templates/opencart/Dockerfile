FROM php:7.2-apache

WORKDIR /var/www/html

RUN a2enmod rewrite

RUN set -xe \
    && apt update \
    && apt install -y libpng-dev libjpeg-dev libmcrypt-dev wget unzip\
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
    && docker-php-ext-install gd mbstring mysqli zip \
    && pecl install mcrypt-1.0.1 \
    && docker-php-ext-enable mcrypt

COPY init.sh /etc/init.sh
RUN chmod 755 /etc/init.sh

CMD ["/etc/init.sh"]