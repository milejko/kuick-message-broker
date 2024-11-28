# syntax=docker/dockerfile:1.6

ARG PHP_VERSION=8.3
ARG OS_VARIANT=jammy

###################################################################
# Base PHP target                                                 #
###################################################################
FROM milejko/php:${PHP_VERSION}-apache-${OS_VARIANT} AS base

###################################################################
# Distribution target (ie. for production environments)           #
###################################################################
FROM base AS dist

# Important performance hint:
# KUICK_APP_ENV=prod should be defined here, or via environment variables
# .env* files shouldn't be used in production
ENV KUICK_APP_ENV=prod \
    KUICK_MB_STORAGE_DSN=file:///var/www/html/var/tmp/messages

# COPY --link ./bin/console ./bin/console
# COPY --link ./etc/di ./etc/di
# COPY --link ./etc/routes ./etc/routes
COPY --link ./etc/apache2 /etc/apache2
COPY --link composer.dist.json composer.json
# COPY --link ./src ./src
# COPY --link ./public/index.php ./public/index.php
# COPY --link ./composer.json .
COPY --link ./version.* .

RUN composer install --no-dev

###################################################################
# Test runner target                                              #
###################################################################
FROM dist AS test-runner

ENV XDEBUG_ENABLE=1 \
    XDEBUG_MODE=coverage \
    KUICK_APP_ENV=dev

COPY --link ./tests ./tests
COPY --link ./php* .

RUN composer install

###################################################################
# Dev server target                                               #
###################################################################
FROM base AS dev-server

COPY ./etc/apache2 /etc/apache2

ENV ENV XDEBUG_ENABLE=1 \
    XDEBUG_MODE=coverage \
    OPCACHE_VALIDATE_TIMESTAMPS=1
