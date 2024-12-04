<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterFactory;
use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterInterface;

use function DI\factory;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    'kuick.app.name'      => 'Kuick.MB',
    // Kuick defaults
    // 'kuick.app.charset'   => 'UTF-8',
    // 'kuick.app.locale'    => 'en_US.utf-8',
    // 'kuick.app.timezone'  => 'UTC',

    //disable valid token
    'kuick.ops.guard.token' => '',

    'kuick.mb.consumer.map' => 'example[]=user@pass',
    'kuick.mb.publisher.map' => 'example[]=user@pass',

    #'kuick.mb.storage.dsn' => 'file:///var/www/html/var/tmp/messages',
    'kuick.mb.storage.dsn' => 'redis://127.0.0.1',

    StorageAdapterInterface::class => factory(StorageAdapterFactory::class),
];
