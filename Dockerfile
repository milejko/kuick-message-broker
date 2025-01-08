# syntax=docker/dockerfile:1.6

ARG PHP_VERSION=8.4 \
    SERVER_VARIANT=apache \
    OS_VARIANT=noble

###################################################################
# Base PHP target                                                 #
###################################################################
FROM milejko/php:${PHP_VERSION}-${SERVER_VARIANT}-${OS_VARIANT} AS base

###################################################################
# Distribution target (ie. for production environments)           #
###################################################################
FROM base AS dist

ENV KUICK_APP_NAME=KuickMB

COPY --link etc/apache2 /etc/apache2
COPY --link config config
COPY --link public public
COPY --link composer.dist.json composer.json
COPY --link version.* public/

RUN set -eux; \
    mkdir -m 777 var; \
    composer install \ 
    --prefer-dist \
    --no-dev \
    --classmap-authoritative \
    --no-scripts \
    --no-plugins

###################################################################
# Test runner target                                              #
###################################################################
FROM base AS test-runner

ENV XDEBUG_ENABLE=1 \
    XDEBUG_MODE=coverage \
    KUICK_APP_ENV=dev

###################################################################
# Dev server target                                               #
###################################################################
FROM base AS dev-server

COPY ./etc/apache2 /etc/apache2

ENV XDEBUG_ENABLE=1 \
    XDEBUG_MODE=coverage \
    OPCACHE_VALIDATE_TIMESTAMPS=1
