<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore;

use Kuick\MessageBroker\Infrastructure\Repositories\RepositoryInterface;

/**
 * Message store
 */
class MessageStore
{
    private const ACK_MAX_TTL = 2592000; //30 days

    private const MESSAGE_PATTERN_TEMPLATE = 'kuickmbmsg-%s-*';
    private const MESSAGE_TEMPLATE = 'kuickmbmsg-%s-%s';
    private const ACK_TEMPLATE = 'kuickmback-%s-%s-%s';

    public function __construct(private RepositoryInterface $repository)
    {
    }

    public function publish(string $channel, string $message, int $ttl = 300): string
    {
        $messageId = $this->generateMessageId();
        $this->repository->set($this->getMessageKey($channel, $messageId), $message, $ttl);
        return $messageId;
    }

    public function getMessages(string $channel, string $userToken): array
    {
        $messages = [];
        foreach ($this->browseMessageKeys($channel) as $messageKey) {
            $messageId = $this->extractMessageIdFromMessageKey($messageKey);
            //already acked
            if ($this->repository->get($this->getAckKey($channel, $messageId, $userToken))) {
                continue;
            }
            $message = $this->repository->get($messageKey);
            unset($message['message']);
            $message['messageId'] = $messageId;
            $messages[] = $message;
        }
        return $messages;
    }

    public function getMessage(string $channel, string $messageId, string $userToken, bool $autoack = false): array
    {
        $message = $this->repository->get($this->getMessageKey($channel, $messageId));
        if (null === $message) {
            throw new MessageNotFoundException();
        }
        //already acked
        if ($this->repository->get($this->getAckKey($channel, $messageId, $userToken))) {
            throw new MessageNotFoundException();
        }
        $message['messageId'] = $messageId;
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
        $this->repository->set($this->getAckKey($channel, $messageId, $userToken), 1, self::ACK_MAX_TTL);
    }

    private function browseMessageKeys(string $channel): array
    {
        return $this->repository->browseKeys(sprintf(self::MESSAGE_PATTERN_TEMPLATE, $channel));
    }

    protected function generateMessageId(): string
    {
        return md5(__CLASS__ . microtime() . mt_rand());
    }

    protected function getMessageKey(string $channel, string $messageId): string
    {
        return sprintf(self::MESSAGE_TEMPLATE, $channel, $messageId);
    }

    protected function extractMessageIdFromMessageKey(string $messageKey): string
    {
        return substr($messageKey, -32);
    }

    protected function getMessagesPattern(string $channel): string
    {
        return sprintf(self::MESSAGE_PATTERN_TEMPLATE, $channel);
    }

    protected function getAckKey(string $channel, string $messageId, string $userToken): string
    {
        return sprintf(self::ACK_TEMPLATE, $channel, $messageId, $userToken);
    }
}
