<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters;

use Nyholm\Dsn\DsnParser;
use Psr\Container\ContainerInterface;
use Redis;

class RedisClientFactory
{
    public static function create(ContainerInterface $container): Redis
    {
        $dsnKey = 'kuick.mb.storage.dsn';
        //configuration key not found
        if (!$container->has($dsnKey)) {
            throw new StorageAdapterException('Redis is missing DSN - config: kuick.mb.repository.dsn or env: KUICK_MB_REPOSITORY_DSN');
        }
        //parse DSN
        $dsn = DsnParser::parse($container->get($dsnKey));
        //create redis client
        $redis = new Redis();
        //pconnect
        $redis->pconnect($dsn->getHost(), $dsn->getPort());
        //authenticate
        $redis->auth(['user' => $dsn->getParameter('user', null), 'pass' => $dsn->getParameter('pass', null)]);
        //select database
        $redis->select($dsn->getParameter('database', 1));
        return $redis;
    }
}
