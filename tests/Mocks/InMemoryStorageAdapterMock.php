<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Kuick\MessageBroker\Mocks;

use Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterException;
use Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterInterface;
use Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters\ValueSerializer;

class InMemoryStorageAdapterMock implements StorageAdapterInterface
{
    private array $storage = [];

    public function get(string $namespace, string $key): ?array
    {
        $serializedValue = $this->storage[$namespace . $key] ?? false;
        if (!$serializedValue) {
            return null;
        }
        $value = ValueSerializer::unserialize($serializedValue);
        //expired
        if ($value['createTime'] + $value['ttl'] < time()) {
            return null;
        }
        return $value;
    }

    public function set(string $namespace, string $key, ?string $value = null, int $ttl = self::MAX_TTL): self
    {
        if ($ttl > self::MAX_TTL) {
            throw new StorageAdapterException("TTL $ttl exceeds " . self::MAX_TTL);
        }
        $this->storage[$namespace . $key] = ValueSerializer::serialize($value, $ttl);
        return $this;
    }

    public function has(string $namespace, string $key): bool
    {
        return null !== $this->get($namespace, $key);
    }

    public function browseKeys(string $namespace, string $pattern = '*'): array
    {
        $keys = [];
        foreach (array_keys($this->storage) as $namespacedKey) {
            $key = substr($namespacedKey, strlen($namespace));
            if (null === $this->get($namespace, $key)) {
                continue;
            }
            $keys[] = $key;
        }
        return $keys;
    }
}
