<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore;

interface StoreInterface
{
    public function publish(string $channel, string $message, int $ttl = 300): string;

    public function getMessage(string $channel, string $messageId, string $userToken, bool $autoack): array;

    public function getMessages(string $channel, string $userToken): array;

    public function ack(string $channel, string $messageId, string $userToken): void;
}
