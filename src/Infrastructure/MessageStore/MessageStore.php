<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Infrastructure\MessageStore;

use KuickMessageBroker\Infrastructure\MessageStore\StorageAdapters\StorageAdapterInterface;

/**
 * Message store
 */
class MessageStore
{
    private const MESSAGE_NAMESPACE_KEY_TEMPLATE = 'kuickmsg-%s';
    private const ACK_NAMESPACE_KEY_TEMPLATE = 'kuickack-%s';

    public function __construct(private StorageAdapterInterface $storageAdapter)
    {
    }

    public function publish(string $channel, string $message, int $ttl = 300): string
    {
        $messageId = md5(__CLASS__ . microtime() . mt_rand());
        $this->storageAdapter->set($this->getMessageNamespace($channel), $messageId, $message, $ttl);
        return $messageId;
    }

    public function getMessages(string $channel, string $userToken): array
    {
        $messages = [];
        $messageKeys = $this->storageAdapter->browseKeys($this->getMessageNamespace($channel));
        foreach ($messageKeys as $messageId) {
            //already acked
            if ($this->isAcked($channel, $messageId, $userToken)) {
                continue;
            }
            $message = $this->storageAdapter->get($this->getMessageNamespace($channel), $messageId);
            unset($message['message']); //we do not want to output a complete message
            $message['messageId'] = $messageId;
            $messages[] = $message;
        }
        return $messages;
    }

    public function getMessage(string $channel, string $messageId, string $userToken): array
    {
        $message = $this->storageAdapter->get($this->getMessageNamespace($channel), $messageId);
        //no message, or already acked
        if (null === $message || $this->isAcked($channel, $messageId, $userToken)) {
            throw new MessageNotFoundException();
        }
        return $message + ['messageId' => $messageId];
    }

    public function ack(string $channel, string $messageId, string $userToken): void
    {
        //make sure that message exists
        $this->getMessage($channel, $messageId, $userToken);
        //write ack with max ttl
        $this->storageAdapter->set($this->getAckNamespace($channel), $messageId . $userToken, '', StorageAdapterInterface::MAX_TTL);
    }

    private function isAcked(string $channel, string $messageId, string $userToken): bool
    {
        return $this->storageAdapter->has($this->getAckNamespace($channel), $messageId . $userToken);
    }

    private function getMessageNamespace(string $namespace): string
    {
        return sprintf(self::MESSAGE_NAMESPACE_KEY_TEMPLATE, $namespace);
    }

    private function getAckNamespace(string $namespace): string
    {
        return sprintf(self::ACK_NAMESPACE_KEY_TEMPLATE, $namespace);
    }
}
