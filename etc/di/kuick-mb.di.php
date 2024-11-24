<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\MessageBroker\Infrastructure\FilesystemStore;
use Kuick\MessageBroker\Infrastructure\StoreInterface;

use function DI\autowire;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    'kuick.mb.consumer.tokens' => [],
    'kuick.mb.publisher.tokens' => [],

    StoreInterface::class => autowire(FilesystemStore::class),
];
