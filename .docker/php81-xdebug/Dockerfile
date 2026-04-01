# hadolint ignore=DL3006
FROM phpunit-agent-reporter-php81

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

ENV XDEBUG_MODE=coverage

ENTRYPOINT ["/bin/bash"]
