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

use function DI\env;
use function DI\factory;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    'kuick.app.name'      => env('KUICK_APP_NAME', 'Kuick.MB'),

    'kuick.mb.consumer.map' => env('KUICK_MB_CONSUMER_MAP', 'example[]=user@pass'),
    'kuick.mb.publisher.map' => env('KUICK_MB_PUBLISHER_MAP', 'example[]=user@pass'),

    'kuick.mb.storage.dsn' => env('KUICK_MB_STORAGE_DSN', 'redis://127.0.0.1'),

    StorageAdapterInterface::class => factory(StorageAdapterFactory::class),
];
