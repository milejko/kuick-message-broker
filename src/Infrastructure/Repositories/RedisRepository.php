<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\Repositories;

use Redis;

class RedisRepository implements RepositoryInterface
{
    public function __construct(private Redis $redis)
    {
    }

    public function get(string $key): ?array
    {
        $content = $this->redis->get($key);
        if (!$content) {
            return null;
        }
        return ValueSerializer::unserialize($content);
    }

    public function set(string $key, ?string $value = null, int $ttl = self::MAX_TTL): RepositoryInterface
    {
        $this->redis->set($key, ValueSerializer::serialize($value, $ttl), $ttl);
        return $this;
    }

    public function has(string $key): bool
    {
        return (bool) $this->redis->exists($key);
    }

    public function browseKeys(string $pattern = '*'): array
    {
        return $this->redis->keys($pattern);
    }
}