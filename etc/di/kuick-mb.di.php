<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\MessageBroker\Infrastructure\MessageStore\RedisStore;
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;

use function DI\autowire;
use function DI\create;
use function DI\env;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    'kuick.mb.consumer.tokens' => [],
    'kuick.mb.publisher.tokens' => [],

    StoreInterface::class => create(RedisStore::class)
        ->constructor(env('KUICK_MB_STORE_REDIS_DSN')),
];
