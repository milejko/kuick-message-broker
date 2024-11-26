<?php

use Kuick\MessageBroker\Infrastructure\MessageStore\FilesystemStore;
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;

use function DI\autowire;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    //query param format: channel[]=user1&channel[]=user2&channel2=user3
    'kuick.mb.consumer.token.map' => '',
    'kuick.mb.publisher.token.map' => '',

    StoreInterface::class => autowire(FilesystemStore::class),
];