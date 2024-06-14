#!/bin/sh
set -o errexit

composer config --global gitlab-token.gitlab.paysera.net glpat-nm1vH6L8FXiE7X7_but5
composer update --prefer-dist --no-interaction
composer dump-autoload --optimize
composer check-platform-reqs
php bin/console cache:warmup
php bin/console doc:fix:load

exec rr serve -c /home/app/src/docker/roadrunner/.rr.dev.yaml
