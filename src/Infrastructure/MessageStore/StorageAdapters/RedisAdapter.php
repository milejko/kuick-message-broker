<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters;

use Redis;

class RedisAdapter implements StorageAdapterInterface
{
    public function __construct(private Redis $redis)
    {
    }

    public function get(string $namespace, string $key): ?array
    {
        $serializedValue = $this->redis->get($namespace . $key);
        if (!$serializedValue) {
            return null;
        }
        return ValueSerializer::unserialize($serializedValue);
    }

    public function set(string $namespace, string $key, ?string $value = null, int $ttl = self::MAX_TTL): self
    {
        if ($ttl > self::MAX_TTL) {
            throw new StorageAdapterException("TTL $ttl exceeds " . self::MAX_TTL);
        }
        $this->redis->set($namespace . $key, ValueSerializer::serialize($value, $ttl), $ttl);
        return $this;
    }

    public function has(string $namespace, string $key): bool
    {
        return (bool) $this->redis->exists($namespace . $key);
    }

    public function browseKeys(string $namespace, string $pattern = '*'): array
    {
        $redisKeys = $this->redis->keys($namespace . $pattern);
        array_walk($redisKeys, function (&$value, $key) use (&$redisKeys, $namespace) {
            $redisKeys[$key] = substr($value, strlen($namespace));
        });
        return $redisKeys;
    }
}
