# Roadrunner Dev Dockerfile
FROM php:8.2-alpine

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        linux-headers \
    && apk add --update --no-cache \
        openssl-dev \
        pcre-dev \
        icu-dev \
        icu-data-full \
        libzip-dev \
        postgresql-dev \
        protobuf \
        grpc \
    && docker-php-ext-install  \
        bcmath \
        intl \
        opcache \
        zip \
        sockets \
        pdo_pgsql \
        pdo_mysql \
    && pecl install protobuf \
    && pecl install grpc \
    && pecl install xdebug \
    && docker-php-ext-enable \
        grpc \
        protobuf \
        xdebug \
    && pecl clear-cache \
    && apk del --purge .build-deps

WORKDIR /home/app/src

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock symfony.lock ./

RUN composer config --global gitlab-token.gitlab.paysera.net glpat-nm1vH6L8FXiE7X7_but5

RUN composer update --no-scripts --no-interaction

#ARG rr_version="latest"
#COPY --from=spiralscout/roadrunner:latest /usr/bin/rr /usr/local/bin

RUN ./vendor/bin/rr get-binary --location /usr/local/bin

ENV APP_ENV=dev

EXPOSE 8080 9000

USER root
COPY ./docker/roadrunner/dev-entrypoint.sh /root/entrypoint.sh
RUN chmod 544 /root/entrypoint.sh

CMD ["/root/entrypoint.sh"]