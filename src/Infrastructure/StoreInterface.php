<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure;

interface StoreInterface
{
    public const MAX_TTL = 2592000; //30 days

    public function publish(string $channel, string $message, int $ttl = 300): string;

    public function getMessages(string $userToken, string $channel): array;

    public function getMessage(string $userToken, string $messageId, string $channel): array;

    public function ack(string $userToken, string $channel, string $messageId): void;
}
