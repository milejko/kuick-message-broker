# syntax=docker/dockerfile:1.6

ARG PHP_VERSION=8.3

###########################################
# Base PHP target                         #
###########################################
FROM milejko/php:${PHP_VERSION}-apache AS base

ENV OPCACHE_VALIDATE_TIMESTAMPS=0 \
    MEMORY_LIMIT=128M

###########################################
# Distribution target                     #
###########################################
FROM base AS dist

COPY . .
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

RUN apt-get update; \
    apt-get install --no-install-recommends -y \
    redis
RUN echo "apc.enable_cli=1" >> /etc/php/${PHP_VERSION}/mods-available/apcu.ini
ENV OPCACHE_VALIDATE_TIMESTAMPS=1

EXPOSE 8080
