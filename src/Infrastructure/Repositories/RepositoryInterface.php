<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\Repositories;

interface RepositoryInterface
{
    public const MAX_TTL = 2592000;

    public function set(string $key, ?string $value = null, int $ttl = self::MAX_TTL): self;

    public function get(string $key): ?array;

    public function has(string $key): bool;

    public function browseKeys(string $pattern = '*'): array;
}