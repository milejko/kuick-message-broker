<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters;

interface StorageAdapterInterface
{
    public const MAX_TTL = 2592000;

    public function set(string $namespace, string $key, ?string $value = null, int $ttl = self::MAX_TTL): self;

    public function get(string $namespace, string $key): ?array;

    public function has(string $namespace, string $key): bool;

    public function browseKeys(string $namespace, string $pattern = '*'): array;
}
