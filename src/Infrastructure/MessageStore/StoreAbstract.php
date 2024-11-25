<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore;

/**
 * Store abstract class
 */
abstract class StoreAbstract implements StoreInterface
{
    protected const MAX_TTL = 2592000; //30 days

    private const MESSAGE_PATTERN_TEMPLATE = 'kuickmbmessage-%s-*';
    private const MESSAGE_TEMPLATE = 'kuickmbmessage-%s-%s';
    private const ACK_TEMPLATE = 'kuickmback-%s-%s-%s';

    protected function generateMessageId(): string
    {
        return md5(__CLASS__ . microtime() . mt_rand());
    }

    protected function getMessageKey(string $channel, string $messageId): string
    {
        return sprintf(self::MESSAGE_TEMPLATE, md5($channel), $messageId);
    }

    protected function extractMessageIdFromMessageKey(string $messageKey): string
    {
        return substr($messageKey, -32);
    }

    protected function getMessagesPattern(string $channel): string
    {
        return sprintf(self::MESSAGE_PATTERN_TEMPLATE, md5($channel));
    }

    protected function getAckKey(string $channel, string $messageId, string $userToken): string
    {
        return sprintf(self::ACK_TEMPLATE, md5($channel), $messageId, md5($userToken));
    }

    protected function serializeMessage(string $message, int $ttl): string
    {
        return json_encode([$message, time(), $ttl]);
    }

    protected function unserializeMessage(string $messageId, string $serializedMessage): array
    {
        $messageArray = json_decode($serializedMessage, true);
        return ['message' => $messageArray[0], 'messageId' => $messageId, 'createTime' => $messageArray[1], 'ttl' => $messageArray[2]];
    }
}
