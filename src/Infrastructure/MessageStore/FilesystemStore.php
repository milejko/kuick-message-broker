<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure\MessageStore;

use FilesystemIterator;
use GlobIterator;

/**
 * Filesystem disk store implementation
 */
class FilesystemStore extends StoreAbstract
{
    private const MESSAGES_FOLDER = '/mb-messages';
    private const ACK_FOLDER = '/mb-acks';
    private const GC_DIVISOR = 100;

    public function __construct(private string $basePath = '/tmp')
    {
    }

    public function publish(string $channel, string $message, int $ttl = 300): string
    {
        $messageId = $this->generateMessageId();
        file_put_contents($this->getMessagesFolder($channel) . $this->getMessageKey($channel, $messageId), $this->serializeMessage($message, $ttl));
        //garbage collector
        $this->gc($channel);
        return $messageId;
    }

    public function getMessages(string $channel, string $userToken): array
    {
        $messages = [];
        $directoryIterator = new GlobIterator($this->getMessagesFolder($channel) . $this->getMessagesPattern($channel), FilesystemIterator::KEY_AS_FILENAME);
        foreach ($directoryIterator as $item) {
            $messageFileName = $item->getPathname();
            $messageId = $this->extractMessageIdFromMessageKey($messageFileName);
            //already acked
            if (file_exists($this->getAcksFolder($channel) . $this->getAckKey($channel, $messageId, $userToken))) {
                continue;
            }
            $message = $this->unserializeMessage($messageId, file_get_contents($messageFileName));
            //validate ttl
            if ($message['createTime'] < time() - $message['ttl']) {
                continue;
            }
            unset($message['message']);
            $messages[] = $message;
        }
        return $messages;
    }

    public function getMessage(string $channel, string $messageId, string $userToken, bool $autoack = false): array
    {
        $messageFileName = $this->getMessagesFolder($channel) . $this->getMessageKey($channel, $messageId);
        if (!file_exists($messageFileName)) {
            throw new MessageNotFoundException();
        }
        if (file_exists($this->getAcksFolder($channel) . $this->getAckKey($channel, $messageId, $userToken))) {
            throw new MessageNotFoundException();
        }
        $message = $this->unserializeMessage($messageId, file_get_contents($messageFileName));
        if ($message['createTime'] < time() - $message['ttl']) {
            throw new MessageNotFoundException();
        }
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
        file_put_contents($this->getAcksFolder($channel) . $this->getAckKey($channel, $messageId, $userToken), null);
    }

    public function gc(string $channel): void
    {
        //skip GC this time
        if (rand(0, self::GC_DIVISOR) != 0) {
            return;
        }
        $messagesIterator = new GlobIterator($this->getMessagesPattern($channel), FilesystemIterator::KEY_AS_FILENAME);
        foreach ($messagesIterator as $messageFile) {
            if ($messageFile->getCTime() > time() - self::MAX_TTL) {
                continue;
            }
            unlink($messageFile->getPathname());
            $acksIterator = new GlobIterator($this->getAcksFolder($channel) . basename($messageFile->getPathname()) . '*', FilesystemIterator::KEY_AS_FILENAME);
            foreach ($acksIterator as $ackFile) {
                unlink($ackFile->getPathname());
            }
        }
    }

    private function getMessagesFolder(string $channel): string
    {
        $dirName = $this->basePath . self::MESSAGES_FOLDER . DIRECTORY_SEPARATOR . md5($channel) . DIRECTORY_SEPARATOR;
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        return $dirName;
    }

    private function getAcksFolder(string $channel): string
    {
        $dirName = $this->basePath . self::ACK_FOLDER . DIRECTORY_SEPARATOR . md5($channel) . DIRECTORY_SEPARATOR;
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        return $dirName;
    }
}
