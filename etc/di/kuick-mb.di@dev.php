<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\MessageBroker\Infrastructure\MessageStore\FilesystemStore;
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;

use function DI\autowire;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    //some config and injections only for dev
    //dummy tokens
    'kuick.mb.consumer.tokens' => [
        'sample-channel' => [
            'user@pass',
            'another-user@pass2',
        ],
    ],
    'kuick.mb.publisher.tokens' => [
        'sample-channel' => [
            'user@pass',
            //another-user can not publish to "sample-channel"
            //'another-user@pass2',
        ],
    ],

    //StoreInterface::class => autowire(FilesystemStore::class),
];