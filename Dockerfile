ARG PHP_VERSION=8.3

FROM milejko/php:${PHP_VERSION}-cli

ENV XDEBUG_ENABLE=1 \
    XDEBUG_MODE=coverage

COPY . .

RUN composer install --no-dev

EXPOSE 8080

WORKDIR /var/www/html/public

CMD [ "php", "-S", "0.0.0.0:8080" ]