<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure;

use APCUIterator;

/**
 * APCu store messenger implementation
 */
class APCUStore implements StoreInterface
{
    private const MESSAGE_TEMPLATE = 'kuickmbmsg-%s%s';
    private const ACK_TEMPLATE = 'kuickmback-%s%s%s';
    private const SEARCH_PATTERN_TEMPLATE = '/^%s$/';

    public function __construct()
    {
        if (!function_exists('apcu_store')) {
            throw new StoreException('APCu not installed or not enabled');
        }
    }

    public function publish(string $channel, string $message, int $ttl = 300): string
    {
        $messageId = $this->generateMessageId();
        apcu_store($this->getMessageKey($channel, $messageId), MessageSerializer::serialize($messageId, $message, $ttl));
        return $messageId;
    }
    
    public function getMessages(string $userToken, string $channel): array
    {
        $messages = [];
        $messagePattern = sprintf(self::MESSAGE_TEMPLATE, md5($channel), '[a-z0-9]{32}');
        foreach ($this->browseAPCU($messagePattern) as $serializedMessage) {
            $message = MessageSerializer::unserialize($serializedMessage);
            //already acked
            if (apcu_fetch($this->getAckKey($channel, $message['messageId'], $userToken))) {
                continue;
            }
            unset($message['message']);
            $messages[] = $message;
        }
        return $messages;
    }

    public function getMessage(string $userToken, string $channel, string $messageId, $autoack = false): array
    {
        $serializedMessage = apcu_fetch($this->getMessageKey($channel, $messageId));
        if (!$serializedMessage) {
            throw new MessageNotFoundException();
        }
        //already acked
        if (apcu_fetch($this->getAckKey($channel, $messageId, $userToken))) {
            throw new MessageNotFoundException();
        }
        if ($autoack) {
            $this->ack($userToken, $channel, $messageId);
        }
        return MessageSerializer::unserialize($serializedMessage) + ['acked' => $autoack];
    }

    public function ack(string $userToken, string $channel, string $messageId): void
    {
        if (32 != strlen($messageId)) {
            throw new MessageNotFoundException();
        }
        //throws Exception if not found
        $this->getMessage($userToken, $channel, $messageId);
        $ackKey = $this->getAckKey($channel, $messageId, $userToken);
        if (apcu_fetch($ackKey)) {
            throw new MessageNotFoundException();
        }
        apcu_store($ackKey, '1', self::MAX_TTL);
    }

    private function getMessageKey(string $channel, string $messageId): string
    {
        return sprintf(self::MESSAGE_TEMPLATE, md5($channel), md5($messageId));
    }

    private function getAckKey(string $channel, string $messageId, string $userToken): string
    {
        return sprintf(self::ACK_TEMPLATE, md5($channel), md5($messageId), md5($userToken));
    }

    private function browseAPCU(string $pattern): array
    {
        $data = [];
        foreach (new APCUIterator(sprintf(self::SEARCH_PATTERN_TEMPLATE, $pattern)) as $item) {
            $data[$item['key']] = $item['value'];
        }
        return $data;
    }

    private function generateMessageId(): string
    {
        return md5(__FILE__ . microtime() . mt_rand());
    }
}
