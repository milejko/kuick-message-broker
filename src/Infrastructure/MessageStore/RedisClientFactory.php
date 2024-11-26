<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore;

use Nyholm\Dsn\DsnParser;
use Psr\Container\ContainerInterface;
use Redis;

class RedisClientFactory
{
    public static function create(ContainerInterface $container): Redis
    {
        //configuration key not found
        if (!$container->has('kuick.mb.store.redis.dsn')) {
            throw new StoreException('Redis is missing DSN - config: kuick.mb.store.redis.dsn or env: KUICK_MB_STORE_REDIS_DSN');
        }
        //parse DSN
        $dsn = DsnParser::parse($container->get('kuick.mb.store.redis.dsn'));
        //create redis client
        $redis = new Redis([
            'host' => $dsn->getHost(),
            'port' => $dsn->getPort(),
            'connectTimeout' => 0.5,
        ]);
        //select database
        $redis->select($dsn->getParameter('database', 1));
        return $redis;
    }
}
