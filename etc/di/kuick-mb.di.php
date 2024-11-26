<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\MessageBroker\Infrastructure\MessageStore\MessageStore;
use Kuick\MessageBroker\Infrastructure\MessageStore\RedisClientFactory;
use Kuick\MessageBroker\Infrastructure\Repositories\FileSystemRepository;
use Kuick\MessageBroker\Infrastructure\Repositories\RedisRepository;
use Kuick\MessageBroker\Infrastructure\Repositories\RepositoryInterface;
use Psr\Container\ContainerInterface;

/**
 * PHP-DI definitions
 * @see https://php-di.org/doc/php-definitions.html
 */
return [
    'kuick.mb.consumer.map' => 'sample-channel[]=user@pass',
    'kuick.mb.publisher.map' => 'sample-channel[]=user@pass',

    RepositoryInterface::class => function(ContainerInterface $container) {
        $repository = $container->has('kuick.mb.repository') ?
            $container->get('kuick.mb.repository') :
            null;
        switch ($repository) {
            case 'redis':
                $redisClient = RedisClientFactory::create($container);
                return new RedisRepository($redisClient);
            default:
                return new FileSystemRepository(BASE_PATH . '/var/tmp/messages');
        }
    },
];
