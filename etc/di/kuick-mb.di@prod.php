<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\MessageBroker\Infrastructure\MessageStore\RedisClientFactory;
use Kuick\MessageBroker\Infrastructure\MessageStore\RedisStore;
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;

use function DI\autowire;
use function DI\factory;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    //redis store for production
    'kuick.mb.store.redis.dsn' => '127.0.0.1:6379?database=1',
    Redis::class => factory([RedisClientFactory::class, 'create']),
    StoreInterface::class => autowire(RedisStore::class),
];
