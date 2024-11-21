<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure;

class MessageSerializer
{
    public static function serialize(string $messageId, string $message, int $ttl): string
    {
        return serialize([$messageId, $message, time(), $ttl]);
    }

    public static function unserialize(string $serializedMessage): array
    {
        $messageArray = unserialize($serializedMessage);
        return [
            'messageId' => $messageArray[0],
            'message' => $messageArray[1],
            'createTime' => $messageArray[2],
            'ttl' => $messageArray[3],
        ];
    }
}
