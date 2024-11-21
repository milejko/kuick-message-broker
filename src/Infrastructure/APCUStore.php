<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure;

/**
 * APCu store messenger implementation
 */
class APCUStore implements StoreInterface
{
    private const MESSAGE_TEMPLATE = 'kuickmbmsg-%s-%s';
    private const ACK_TEMPLATE = 'kuickmback-%s-%s-%s';
    private const SEARCH_PATTERN_TEMPLATE = '/^%s$/';
    private const MAX_TTL = 1209600; //14 days

    public function publish(string $channel, string $message, int $ttl = 300): string
    {
        if (!(new ObjectNameValidator)->isValid($channel)) {
            throw new ValidationException('Channel name invalid');
        }
        $messageId = $this->generateMessageId();
        apcu_store(sprintf(self::MESSAGE_TEMPLATE, $channel, $messageId), serialize([$message, $messageId, time(), $ttl]), $ttl);
        return $messageId;
    }
    
    public function getMessages(string $userToken, string $channel): array
    {
        if (!(new ObjectNameValidator)->isValid($userToken)) {
            throw new ValidationException('User token invalid format');
        }
        if (!(new ObjectNameValidator)->isValid($channel)) {
            throw new ValidationException('Chanel name invalid');
        }
        $messages = [];
        $messagePattern = sprintf(self::MESSAGE_TEMPLATE, $channel, '[a-z0-9]{32}');
        foreach ($this->searchApc($messagePattern) as $messageKey => $serializedMessage) {
            $messageId = substr($messageKey, -32);
            $ackKey = sprintf(self::ACK_TEMPLATE, $channel, $messageId, $userToken);
            if (apcu_fetch($ackKey)) {
                continue;
            }
            $message = $this->formatSerializedMessage($serializedMessage);
            unset($message['message']);
            $messages[] = $message;
        }
        return $messages;
    }

    public function getMessage(string $userToken, string $channel, string $messageId, $autoack = false): array
    {
        if (!(new ObjectNameValidator)->isValid($userToken) || !(new ObjectNameValidator)->isValid($channel)) {
            throw new ValidationException('Chanel name invalid');
        }
        if (32 != strlen($messageId)) {
            throw new ValidationException('Message ID is invalid');
        }
        $serializedMessage = apcu_fetch(sprintf(self::MESSAGE_TEMPLATE, $channel, $messageId));
        if (!$serializedMessage) {
            throw new NotFoundException();
        }
        //already acked
        if (apcu_fetch(sprintf(self::ACK_TEMPLATE, $channel, $messageId, $userToken))) {
            throw new NotFoundException();
        }
        if ($autoack) {
            $this->ack($userToken, $channel, $messageId);
        }
        $message = $this->formatSerializedMessage($serializedMessage);
        $message['acked'] = $autoack;
        return $message;
    }

    public function ack(string $userToken, string $channel, string $messageId): void
    {
        if (!(new ObjectNameValidator)->isValid($userToken)) {
            throw new ValidationException('User token invalid format');
        }
        if (32 != strlen($messageId)) {
            throw new ValidationException('Message ID is invalid');
        }
        $ackKey = sprintf(self::ACK_TEMPLATE, $channel, $messageId, $userToken);
        if (apcu_fetch($ackKey)) {
            throw new NotFoundException();
        }
        apcu_store($ackKey, '1', self::MAX_TTL);
    }

    private function formatSerializedMessage(string $serializedMessage): array
    {
        $messageArray = unserialize($serializedMessage);
        return ['message' => $messageArray[0], 'messageId' => $messageArray[1], 'created' => date('Y-m-d H:i:s', $messageArray[2]), 'ttl' => $messageArray[3]];
    }

    private function searchApc(string $pattern): array
    {
        $data = [];
        foreach (new \APCUIterator(sprintf(self::SEARCH_PATTERN_TEMPLATE, $pattern)) as $item) {
            $data[$item['key']] = $item['value'];
        }
        return $data;
    }

    private function generateMessageId(): string
    {
        return md5(__FILE__ . microtime() . mt_rand());
    }
}
