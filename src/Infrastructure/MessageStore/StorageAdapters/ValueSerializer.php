<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore\StorageAdapters;

class ValueSerializer
{
    public static function serialize(?string $value, int $ttl): string
    {
        return json_encode([$value, time(), $ttl]);
    }

    public static function unserialize(string $serializedValue): array
    {
        $valueArray = json_decode($serializedValue, true);
        return ['message' => $valueArray[0], 'createTime' => $valueArray[1], 'ttl' => $valueArray[2]];
    }
}