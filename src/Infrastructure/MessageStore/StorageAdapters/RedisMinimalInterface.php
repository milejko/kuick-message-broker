<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters;

interface RedisMinimalInterface
{
    public function set(string $key, ?string $value = null, int $ttl = 0): self;

    public function get(string $key): ?string;

    public function exists(string $key): bool;

    public function keys(string $pattern = '*'): array;
}
