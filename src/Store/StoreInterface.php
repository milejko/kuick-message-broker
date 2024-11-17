<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Store;

interface StoreInterface
{
    public function publish(string $channel, string $message, int $ttl = 60): string;
    public function getMessages(string $userName, string $channel): array;
    public function ack(string $userName, string $channel, string $messageId): bool;
}