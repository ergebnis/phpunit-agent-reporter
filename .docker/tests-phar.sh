#!/usr/bin/env bash

set -o errexit

PHPUNIT_VERSION="${1}"

if [ -z "${PHPUNIT_VERSION}" ]; then
    echo "Usage: tests-phar.sh <phpunit-version>"
    echo "  phpunit-version: e.g. 10.0.0, 11.0.0, 12.0.0, 13.0.0"
    exit 1
fi

case "${PHPUNIT_VERSION}" in
    10.0.0) DIRECTORY="PHPUnit10" ;;
    11.0.0) DIRECTORY="PHPUnit11" ;;
    12.0.0) DIRECTORY="PHPUnit12" ;;
    13.0.0) DIRECTORY="PHPUnit13" ;;
    *)
        echo "Unknown PHPUnit version: ${PHPUNIT_VERSION}"
        exit 1
        ;;
esac

cp --archive /app/src/. /app/work
cd /app/work

composer remove ergebnis/composer-normalize ergebnis/license ergebnis/php-cs-fixer-config ergebnis/phpstan-rules ergebnis/rector-rules infection/infection phpstan/extension-installer phpstan/phpstan phpstan/phpstan-deprecation-rules phpstan/phpstan-phpunit phpstan/phpstan-strict-rules rector/rector --ansi --dev --no-interaction --no-progress --quiet
composer config platform.php --unset

composer require "phpunit/phpunit:^${PHPUNIT_VERSION}" --ansi --no-interaction --no-progress --quiet --update-with-all-dependencies
composer update --ansi --no-interaction --no-progress --quiet

php bin/remove-autoload-configuration.php
composer install --ansi --no-interaction --no-progress --quiet

vendor/bin/phpunit --colors=always --configuration="test/Phar/${DIRECTORY}/phpunit.xml"
