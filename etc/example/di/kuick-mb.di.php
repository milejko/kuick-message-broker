<?php

use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterFactory;
use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterInterface;

use function DI\factory;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    //common injections and values (params)

    //query param format: channel[]=user1&channel[]=user2&channel2=user3
    'kuick.mb.consumer.map' => 'example[]=user@pass',
    'kuick.mb.publisher.map' => 'example[]=user@pass',

    'kuick.mb.storage.dsn' => 'file:///var/www/html/var/tmp/messages',

    StorageAdapterInterface::class => factory(StorageAdapterFactory::class),
];