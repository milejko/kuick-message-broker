<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\MessageBroker\Store;

use MessageBroker\Store\DiskStore;
use MessageBroker\Store\StoreException;

/**
 * Disk backed publisher
 */
class DiskStoreTest extends \PHPUnit\Framework\TestCase
{
    public function testIfStandardFlowWorksCorrectly(): void
    {
        $dm = new DiskStore();
        $userName = 'john-doe@my_very_secret.1789';
        $channel = 'sample-channel';

        $messageId = $dm->publish($channel, 'sample message', 60);
        self::assertNotEmpty($messageId);

        $messages = $dm->getMessages($userName, $channel);
        self::assertNotEmpty($messages);

        foreach ($messages as $messageId => $messageContents) {
            self::assertEquals('sample message', $messageContents);
            $dm->ack($userName, $channel, $messageId);
        }
        self::assertEquals([], $dm->getMessages($userName, $channel));
    }

    public function testIfBrokenChannelNameCanNotBeUsed(): void
    {
        $dm = new DiskStore();

        $this->expectException(StoreException::class);
        $dm->publish('invalid-characters-in-channel()', '', 60);
    }

    public function testIfBrokenUserNameCanNotBeUsed(): void
    {
        $dm = new DiskStore();

        $this->expectException(StoreException::class);
        $dm->getMessages('broken-user-name^&)', 'some-channel');
    }
}