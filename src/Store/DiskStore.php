<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Store;

/**
 * Local disk storage messenger implementation
 */
class DiskStore implements StoreInterface
{
    private const MESSAGE_PREFIX = 'msg-';
    private const MESSAGE_FILENAME_TEMPLATE = self::MESSAGE_PREFIX . '%s-%s';
    private const MESSAGES_FOLDER = BASE_PATH . '/var/messages';
    private const ACK_FOLDER = BASE_PATH . '/var/ack';
    private const GC_DIVISOR = 100;

    public function publish(string $channel, string $message, int $ttl = 60): string
    {
        if (!(new ObjectNameValidator)->isValid($channel)) {
            throw new StoreException();
        }
        if (0 == rand(0, self::GC_DIVISOR)) {
            $this->gc($channel);
        }
        $messageId = $this->generateMessageId();
        $fileName = $this->getMessagesFolder($channel) . sprintf(self::MESSAGE_FILENAME_TEMPLATE, $messageId, $ttl);
        file_put_contents($fileName, $message);
        return $messageId;
    }
    
    public function getMessages(string $userToken, string $channel): array
    {
        if (!(new ObjectNameValidator)->isValid($userToken) || !(new ObjectNameValidator)->isValid($channel)) {
            throw new StoreException();
        }
        $messages = [];
        foreach (glob($this->getMessagesFolder($channel). self::MESSAGE_PREFIX . '*') as $messageFile) {
            if (file_exists($this->getAcksFolder($channel) . basename($messageFile) . '-' . $userToken)) {
                continue;
            }
            $messageId = explode('-', basename($messageFile))[1];
            $ttl = explode('-', basename($messageFile))[2];
            if (filemtime($messageFile) < time() - $ttl) {
                continue;
            }
            $messages[$messageId] = file_get_contents($messageFile);
        }
        return $messages;
    }

    public function ack(string $userToken, string $channel, string $messageId): bool
    {
        if (!(new ObjectNameValidator)->isValid($userToken)) {
            throw new StoreException();
        }
        $fileName = $this->getMessagesFolder($channel) . sprintf(self::MESSAGE_FILENAME_TEMPLATE, $messageId, '*');
        foreach (glob($fileName) as $messageFile) {
            $ackFileName = $this->getAcksFolder($channel) . basename($messageFile) . '-' . $userToken;
            if (file_exists($ackFileName)) {
                return false;
            }
            file_put_contents($ackFileName, '');
            return true;
        }
        return false;
    }

    public function gc(string $channel): void
    {
        foreach (glob($this->getMessagesFolder($channel) . self::MESSAGE_PREFIX . '*') as $messageFile) {
            $ttl = substr($messageFile, strrpos($messageFile, '-') + 1);
            if (filemtime($messageFile) > time() - $ttl) {
                continue;
            }
            unlink($messageFile);
            foreach (glob($this->getAcksFolder($channel) . basename($messageFile) . '*') as $ackFile) {
                unlink($ackFile);
            }
        }
    }

    private function getMessagesFolder(string $channel): string
    {
        $dirName = self::MESSAGES_FOLDER . '/' . $channel . '/';
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }
        return $dirName;
    }

    private function getAcksFolder(string $channel): string
    {
        $dirName = self::ACK_FOLDER . '/' . $channel . '/';
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