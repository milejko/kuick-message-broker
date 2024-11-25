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
 * Redis store messenger implementation
 */
class RedisStore extends StoreAbstract
{
    /** @disregard P1009 Undefined type */
    public function __construct(private \Redis $redis)
    {
        //$redis->flushAll();
    }

    public function publish(string $channel, string $message, int $ttl = 300): string
    {
        $messageId = $this->generateMessageId();
        $this->redis->set($this->getMessageKey($channel, $messageId), $this->serializeMessage($message, $ttl), $ttl);
        return $messageId;
    }

    public function getMessages(string $channel, string $userToken): array
    {
        $messages = [];
        foreach ($this->browseMessageKeys($channel) as $messageKey) {
            $messageId = $this->extractMessageIdFromMessageKey($messageKey);
            //already acked
            if ($this->redis->get($this->getAckKey($channel, $messageId, $userToken))) {
                continue;
            }
            $serializedMessage = $this->redis->get($messageKey);
            $message = $this->unserializeMessage($messageId, $serializedMessage);
            unset($message['message']);
            $messages[] = $message;
        }
        return $messages;
    }

    public function getMessage(string $channel, string $messageId, string $userToken, $autoack = false): array
    {
        $serializedMessage = $this->redis->get($this->getMessageKey($channel, $messageId));
        if (!$serializedMessage) {
            throw new MessageNotFoundException();
        }
        //already acked
        if ($this->redis->get($this->getAckKey($channel, $messageId, $userToken))) {
            throw new MessageNotFoundException();
        }
        $message = $this->unserializeMessage($messageId, $serializedMessage);
        if ($autoack) {
            $this->ack($channel, $messageId, $userToken);
        }
        return $message + ['acked' => $autoack];
    }

    public function ack(string $channel, string $messageId, string $userToken): void
    {
        //make sure that message exists
        $this->getMessage($channel, $messageId, $userToken);
        //write ack
        $this->redis->set($this->getAckKey($channel, $messageId, $userToken), 1, self::MAX_TTL);
    }

    private function browseMessageKeys(string $channel): array
    {
        return $this->redis->keys($this->getMessagesPattern($channel));
    }
}
