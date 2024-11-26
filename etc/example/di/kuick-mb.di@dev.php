<?php

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */

use Kuick\MessageBroker\Infrastructure\MessageStore\FilesystemStore;
use Kuick\MessageBroker\Infrastructure\MessageStore\StoreInterface;

use function DI\autowire;

return [
    //some dummy tokens
    //query param format: channel[]=user1&channel[]=user2&channel2=user3
    'kuick.mb.consumer.channel.token.map' => 'sample-channel[]:john@pass&another-channel[]jane@pass&another-channel[]=john@pass',
    'kuick.mb.publisher.channel.token.map' => 'sample-channel[]:john@pass&another-channel[]jane@pass&another-channel[]=john@pass',

    //filesystem store for dev
    StoreInterface::class => autowire(FilesystemStore::class),
];