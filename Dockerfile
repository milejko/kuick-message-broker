# syntax=docker/dockerfile:1.6

ARG PHP_VERSION=8.3

###########################################
# Base PHP target                         #
###########################################
FROM milejko/php:${PHP_VERSION}-apache-noble AS base

ENV OPCACHE_VALIDATE_TIMESTAMPS=0 \
    MEMORY_LIMIT=128M

###########################################
# Distribution target                     #
###########################################
FROM base AS dist

COPY ./bin ./bin
COPY ./src ./src
COPY ./public ./public
COPY ./etc/di ./etc/di
COPY ./etc/routes ./etc/routes
COPY ./composer.* ./
COPY ./version.txt ./version.txt
COPY ./etc/apache2 /etc/apache2

RUN composer install --no-dev

###########################################
# Test runner target                      #
###########################################
FROM dist AS test-runner

ENV XDEBUG_ENABLE=1 \
    XDEBUG_MODE=coverage

RUN apt-get update; \
    apt-get install --no-install-recommends -y \
    redis
RUN echo "apc.enable_cli=1" >> /etc/php/${PHP_VERSION}/mods-available/apcu.ini
RUN composer install --dev

###########################################
# Dev server target                       #
###########################################
FROM base AS dev-server

COPY ./etc/apache2 /etc/apache2

ENV OPCACHE_VALIDATE_TIMESTAMPS=1 \
    XDEBUG_ENABLE=1 \
    XDEBUG_MODE=coverage

RUN apt-get update; \
    apt-get install --no-install-recommends -y \
    redis

EXPOSE 8080
