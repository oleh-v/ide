FROM php:7.3.2-apache-stretch

RUN apt-get update
RUN apt install libltdl7 mc -y

RUN mkdir /host
RUN chown -R 777 /host