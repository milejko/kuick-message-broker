<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\KuickMessageBroker\Mocks;

use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\RedisMinimalInterface;

class RedisMock implements RedisMinimalInterface
{
    private array $storage = [];
    private array $createTimes = [];
    private array $ttls = [];

    public function get(string $key): ?string
    {
        return $this->exists($key) ? $this->storage[$key] : null;
    }

    public function set(string $key, ?string $value = null, int $ttl = 0): self
    {
        $this->storage[$key] = $value;
        $this->createTimes[$key] = time();
        $this->ttls[$key] = $ttl;
        return $this;
    }

    public function exists(string $key): bool
    {
        if (!isset($this->ttls[$key]) || !isset($this->storage[$key]) || !isset($this->createTimes[$key])) {
            return false;
        }
        //failed ttl
        if ($this->ttls[$key] + $this->createTimes[$key] <= time()) {
            return false;
        }
        return $this->storage[$key];
    }

    public function keys(string $pattern = '*'): array
    {
        $keys = [];
        foreach (array_keys($this->storage) as $key) {
            if (null === $this->exists($key)) {
                continue;
            }
            $keys[] = $key;
        }
        return $keys;
    }
}
