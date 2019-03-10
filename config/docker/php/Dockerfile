FROM php:7.3.2-apache-stretch

RUN apt-get update
RUN apt install libltdl7 mc -y

RUN docker-php-ext-install mysqli

RUN docker-php-ext-enable mysqli

RUN echo '[Date]' >> /usr/local/etc/php/php.ini
RUN echo 'date.timezone = Europe/Kiev' >> /usr/local/etc/php/php.ini

RUN mkdir /host
RUN chmod -R 777 /host

ARG docker_gid
ENV docker_gid=$docker_gid

RUN groupmod -g $docker_gid www-data