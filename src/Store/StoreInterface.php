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
    public function publish(string $channel, string $message, int $ttl = 300): string;
    public function getMessages(string $userToken, string $channel): array;
    public function getMessage(string $userToken, string $messageId, string $channel): array;
    public function ack(string $userToken, string $channel, string $messageId): void;
}