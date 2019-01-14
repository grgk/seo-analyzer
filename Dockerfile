FROM php:7.2-alpine

RUN apk update && \
  apk upgrade && \
  apk add \
    php7-mbstring \
    php7-bcmath \
    bash \
    git && \
  ln -sf \
    /usr/bin/php7 \
    /usr/bin/php && \
  rm -rf \
    /var/cache/apk/* \
    /etc/php7/*

RUN docker-php-ext-install mbstring bcmath

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /opt/app/

COPY . /opt/app
