#!/usr/bin/env bash

set -o errexit

cp --archive /app/src/. /app/work
cd /app/work

composer install --ansi --no-interaction --no-progress --quiet

vendor/bin/phpunit --colors=always --configuration=test/Unit/phpunit.xml
