<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters\FileAdapter;
use Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters\RedisAdapter;
use Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters\RedisClientFactory;
use Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    'kuick.mb.consumer.map' => 'sample-channel[]=user@pass',
    'kuick.mb.publisher.map' => 'sample-channel[]=user@pass',

    StorageAdapterInterface::class => function(ContainerInterface $container) {
        $logger = $container->get(LoggerInterface::class);
        $adapterKey = 'kuick.mb.storage.adapter';
        switch ($container->has($adapterKey) ? $container->get($adapterKey) : null) {
            case 'redis':
                $logger->info('Redis storage adapter selected');
                return new RedisAdapter(RedisClientFactory::create($container));
            default:
                $logger->info('Default file storage adapter selected');
                $pathKey = 'kuick.mb.storage.path';
                $path = $container->has($pathKey) ? $container->get($pathKey) : BASE_PATH . '/var/tmp/messages';
                return new FileAdapter($path);
        }
    },
];
