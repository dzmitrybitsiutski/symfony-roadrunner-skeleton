# Roadrunner Dev Dockerfile
FROM php:8.3-cli

ENV PROTOBUF_VERSION "3.25.1"

RUN apt-get update && apt-get install -y --no-install-recommends \
  nano \
  bash \
  libzip-dev \
  unzip \
  libonig-dev

# Install PHP Extensions
ENV CFLAGS="$CFLAGS -D_GNU_SOURCE"
RUN docker-php-ext-install zip \
  && docker-php-ext-install opcache sockets mbstring bcmath \
  && docker-php-ext-enable opcache sockets mbstring bcmath

# Protobuf and GRPC
ENV PROTOBUF_VERSION "3.25.1"
RUN pecl channel-update pecl.php.net
RUN pecl install protobuf-${PROTOBUF_VERSION} grpc \
    && docker-php-ext-enable protobuf grpc

WORKDIR /home/app/src

# Install Temporal CLI
COPY --from=temporalio/admin-tools /usr/local/bin/tctl /usr/local/bin/tctl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock symfony.lock ./

RUN composer config --global gitlab-token.gitlab.paysera.net glpat-nm1vH6L8FXiE7X7_but5

RUN composer update --no-scripts --no-interaction

#ARG rr_version="latest"
#COPY --from=spiralscout/roadrunner:latest /usr/bin/rr /usr/local/bin

#RUN ./vendor/bin/rr get-binary --location /usr/local/bin

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