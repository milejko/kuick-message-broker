<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters;

use Nyholm\Dsn\Configuration\Dsn;
use Redis;
use Throwable;

class RedisClientFactory
{
    private const DEFAULT_PORT = 6379;

    public function __invoke(Dsn $dsn): RedisAdapter
    {
        //new redis client
        $redis = new Redis();
        //try pconnect
        try {
            $redis->pconnect($dsn->getHost(), $dsn->getPort() ?? self::DEFAULT_PORT);
        } catch (Throwable $error) {
            throw new StorageAdapterException($error->getMessage() . ': ' . $dsn->getScheme() . '://' . $dsn->getHost() . ':' . ($dsn->getPort() ?? self::DEFAULT_PORT));
        }
        //optional authentication
        if ($dsn->getParameter('user') || $dsn->getParameter('pass')) {
            $redis->auth(['user' => $dsn->getParameter('user', null), 'pass' => $dsn->getParameter('pass', null)]);
        }
        //optional database selection
        if ($dsn->getParameter('database')) {
            $redis->select($dsn->getParameter('database'));
        }
        return new RedisAdapter($redis);
    }
}
