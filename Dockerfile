# Use the official PHP 8.4 with Apache image as the base
FROM php:8.4-apache

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install basic tools tools
RUN apt-get update && apt-get install -y \
        git \
        zip \
        iputils-ping \
        curl

# Install PHP extensions
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions bcmath curl mysqli mbstring zip intl pdo_mysql

# install composer
RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
RUN php -r "if (hash_file('sha384', '/tmp/composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php /tmp/composer-setup.php
RUN php -r "unlink('/tmp/composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# enable mod_rewrite
RUN a2enmod rewrite
RUN apache2ctl restart
