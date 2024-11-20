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
 * Local disk store messenger implementation
 */
class DiskStore implements StoreInterface
{
    private const MESSAGE_PREFIX = 'msg-';
    private const MESSAGE_FILENAME_TEMPLATE = self::MESSAGE_PREFIX . '%s-%s';
    private const MESSAGES_FOLDER = BASE_PATH . '/var/messages';
    private const ACK_FOLDER = BASE_PATH . '/var/acks';
    private const GC_DIVISOR = 100;

    public function publish(string $channel, string $message, int $ttl = 300): string
    {
        if (!(new ObjectNameValidator)->isValid($channel)) {
            throw new ValidationException('Channel name invalid');
        }
        if (0 == rand(0, self::GC_DIVISOR)) {
            $this->gc($channel);
        }
        $messageId = $this->generateMessageId();
        $messageFile = $this->getMessagesFolder($channel) . sprintf(self::MESSAGE_FILENAME_TEMPLATE, $messageId, $ttl);
        file_put_contents($messageFile, $message);
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
        foreach (glob($this->getMessagesFolder($channel). self::MESSAGE_PREFIX . '*') as $messageFile) {
            if (file_exists($this->getAcksFolder($channel) . basename($messageFile) . '-' . $userToken)) {
                continue;
            }
            $messageId = explode('-', basename($messageFile))[1];
            $ttl = explode('-', basename($messageFile))[2];
            $timeCreated = filectime($messageFile);
            if ($timeCreated < time() - $ttl) {
                continue;
            }
            $messages[] = ['messageId' => $messageId, 'created' => date('Y-m-d H:i:s', $timeCreated), 'ttl' => $ttl];
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
        foreach (glob($this->getMessagesFolder($channel). sprintf(self::MESSAGE_FILENAME_TEMPLATE, $messageId, '*')) as $messageFile) {
            if (file_exists($this->getAcksFolder($channel) . basename($messageFile) . '-' . $userToken)) {
                throw new NotFoundException();
            }
            $messageId = explode('-', basename($messageFile))[1];
            $ttl = explode('-', basename($messageFile))[2];
            $timeCreated = filectime($messageFile);
            if ($timeCreated < time() - $ttl) {
                throw new NotFoundException();
            }
            $message = file_get_contents($messageFile);
            if ($autoack) {
                $this->ack($userToken, $channel, $messageId);
            }
            return ['message' => $message, 'messageId' => $messageId, 'created' => date('Y-m-d H:i:s', $timeCreated), 'ttl' => $ttl];
        }
        throw new NotFoundException();
    }

    public function ack(string $userToken, string $channel, string $messageId): void
    {
        if (!(new ObjectNameValidator)->isValid($userToken)) {
            throw new ValidationException('User token invalid format');
        }
        if (32 != strlen($messageId)) {
            throw new ValidationException('Message ID is invalid');
        }
        foreach (glob($this->getMessagesFolder($channel) . sprintf(self::MESSAGE_FILENAME_TEMPLATE, $messageId, '*')) as $messageFile) {
            $ackFileName = $this->getAcksFolder($channel) . basename($messageFile) . '-' . $userToken;
            if (file_exists($ackFileName)) {
                throw new NotFoundException();
            }
            file_put_contents($ackFileName, '');
            return;
        }
        throw new NotFoundException();
    }

    public function gc(string $channel): void
    {
        foreach (glob($this->getMessagesFolder($channel) . self::MESSAGE_PREFIX . '*') as $messageFile) {
            $ttl = substr($messageFile, strrpos($messageFile, '-') + 1);
            if (filectime($messageFile) > time() - $ttl) {
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