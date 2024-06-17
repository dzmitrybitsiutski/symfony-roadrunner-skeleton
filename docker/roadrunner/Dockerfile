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

# Install Temporal CLI
COPY --from=temporalio/admin-tools /usr/local/bin/tctl /usr/local/bin/tctl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock symfony.lock ./

RUN composer config --global gitlab-token.gitlab.paysera.net glpat-nm1vH6L8FXiE7X7_but5

RUN composer install --no-scripts --no-interaction

ENV APP_ENV=dev

EXPOSE 8080 9000

# Setup RoadRunner
RUN vendor/bin/rr get --no-interaction \
    && mv rr /usr/local/bin/rr \
    && chmod +x /usr/local/bin/rr

USER root
COPY ./docker/roadrunner/dev-entrypoint.sh /root/entrypoint.sh
RUN chmod 544 /root/entrypoint.sh

# Wait for Temporal service to star up
COPY ./docker/roadrunner/wait-for-temporal.sh /root/wait-for-temporal.sh
RUN chmod 544 /root/wait-for-temporal.sh

CMD ["/root/entrypoint.sh"]