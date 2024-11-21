<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Kuick\MessageBroker\Infrastructure;

use FilesystemIterator;
use GlobIterator;

/**
 * Filesystem disk store implementation
 */
class FilesystemStore implements StoreInterface
{
    private const MESSAGE_PREFIX = 'msg-';
    private const MESSAGE_FILENAME_TEMPLATE = self::MESSAGE_PREFIX . '%s';
    private const MESSAGES_FOLDER = BASE_PATH . '/var/messages';
    private const ACK_FOLDER = BASE_PATH . '/var/acks';
    private const GC_DIVISOR = 100;

    public function publish(string $channel, string $message, int $ttl = 300): string
    {
        $messageId = $this->generateMessageId();
        $messageFileName = $this->getMessagesFolder($channel) . sprintf(self::MESSAGE_FILENAME_TEMPLATE, $messageId);
        file_put_contents($messageFileName, MessageSerializer::serialize($messageId, $message, $ttl));
        //garbage collector
        if (0 == rand(0, self::GC_DIVISOR)) {
            $this->gc($channel);
        }
        return $messageId;
    }
    
    public function getMessages(string $userToken, string $channel): array
    {
        $messages = [];
        $directoryPattern = $this->getMessagesFolder($channel). self::MESSAGE_PREFIX . '*';
        $directoryIterator = new GlobIterator($directoryPattern, FilesystemIterator::KEY_AS_FILENAME);
        foreach ($directoryIterator as $item) {
            $messageFileName = $item->getPathname();
            if (file_exists($this->getAcksFolder($channel) . basename($messageFileName) . md5($userToken))) {
                continue;
            }
            $message = MessageSerializer::unserialize(file_get_contents($messageFileName));
            if ($message['createTime'] < time() - $message['ttl']) {
                continue;
            }
            unset($message['message']);
            $messages[] = $message;
        }
        return $messages;
    }

    public function getMessage(string $userToken, string $channel, string $messageId, $autoack = false): array
    {
        $messageFileName = $this->getMessagesFolder($channel). sprintf(self::MESSAGE_FILENAME_TEMPLATE, $messageId);
        if (!file_exists($messageFileName)) {
            throw new MessageNotFoundException();
        }
        if (file_exists($this->getAcksFolder($channel) . basename($messageFileName) . md5($userToken))) {
            throw new MessageNotFoundException();
        }
        $message = MessageSerializer::unserialize(file_get_contents($messageFileName));
        if ($message['createTime'] < time() - $message['ttl']) {
            throw new MessageNotFoundException();
        }
        if ($autoack) {
            $this->ack($userToken, $channel, $messageId);
        }
        return $message + ['acked' => $autoack];
    }

    public function ack(string $userToken, string $channel, string $messageId): void
    {
        if (32 != strlen($messageId)) {
            throw new MessageNotFoundException();
        }
        $messageFileName = $this->getMessagesFolder($channel) . sprintf(self::MESSAGE_FILENAME_TEMPLATE, $messageId);
        if (!file_exists($messageFileName)) {
            throw new MessageNotFoundException();
        }
        $ackFileName = $this->getAcksFolder($channel) . basename($messageFileName) . md5($userToken);
        if (file_exists($ackFileName)) {
            throw new MessageNotFoundException();
        }
        file_put_contents($ackFileName, '');
    }

    public function gc(string $channel): void
    {
        $messagesIterator = new GlobIterator($this->getMessagesFolder($channel) . self::MESSAGE_PREFIX . '*', FilesystemIterator::KEY_AS_FILENAME);
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
        $dirName = self::MESSAGES_FOLDER . DIRECTORY_SEPARATOR . md5($channel) . DIRECTORY_SEPARATOR;
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        return $dirName;
    }

    private function getAcksFolder(string $channel): string
    {
        $dirName = self::ACK_FOLDER . DIRECTORY_SEPARATOR . md5($channel) . DIRECTORY_SEPARATOR;
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        return $dirName;
    }

    private function generateMessageId(): string
    {
        return md5(self::MESSAGES_FOLDER . microtime() . mt_rand());
    }
}
