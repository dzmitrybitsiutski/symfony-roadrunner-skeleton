#!/bin/sh
composer update --prefer-dist --no-interaction && \
composer dump-autoload --optimize && \
composer check-platform-reqs && \
php bin/console cache:warmup && \
php bin/console  doc:fix:load && \
rr serve -c /home/app/src/docker/roadrunner/.rr.dev.yaml