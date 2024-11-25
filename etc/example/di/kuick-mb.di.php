<?php

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