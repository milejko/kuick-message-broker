<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Kuick\MessageBroker\Store;

use Kuick\MessageBroker\Infrastructure\APCUStore;
use Kuick\MessageBroker\Infrastructure\NotFoundException;

class APCUStoreTest extends \PHPUnit\Framework\TestCase
{
    public function testIfEmptyStoreIsEmpty(): void
    {
        $store = new APCUStore();
        self::assertEmpty($store->getMessages('xyz', 'channel'));
    }

    public function testIfMessageGetsPublished(): void
    {
        $store = new APCUStore();
        $userToken = 'john-doe@my_very_secret.1789';
        $channel = 'sample-channel';
        $messageId = $store->publish($channel, 'some message', 321);
        self::assertNotEmpty($messageId);
        $readMessage = $store->getMessage($userToken, $channel, $messageId);
        self::assertEquals('some message', $readMessage['message']);
        self::assertEquals(321, $readMessage['ttl']);
        self::assertEquals($messageId, $readMessage['messageId']);
    }

    public function testIfMessagesAreVisibleOnAList(): void
    {
        $store = new APCUStore();
        $userToken = 'frank@pass';
        $channel = 'another-channel';
        $messageIds[] = $store->publish($channel, 'some message');
        $messageIds[] = $store->publish($channel, 'other message');
        $messageIds[] = $store->publish($channel, 'yet another');

        $readMessages = $store->getMessages($userToken, $channel);
        self::assertCount(3, $readMessages);

        $firstMessage = $store->getMessage($userToken, $channel, $messageIds[0]);
        $secondMessage = $store->getMessage($userToken, $channel, $messageIds[1]);
        $thirdMessage = $store->getMessage($userToken, $channel, $messageIds[2]);

        self::assertEquals('some message', $firstMessage['message']);
        self::assertEquals('other message', $secondMessage['message']);
        self::assertEquals('yet another', $thirdMessage['message']);
    }

    public function testIfAutoAckWorks(): void
    {
        $store = new APCUStore();
        $userToken = 'alice@pass';
        $channel = 'yet-another-channel';
        //autoack
        $messageId = $store->publish($channel, 'some message');
        self::assertEquals('some message', $store->getMessage($userToken, $channel, $messageId, true)['message']);
        self::assertEmpty($store->getMessages($userToken, $channel));
        $this->expectException(NotFoundException::class);
        $store->getMessage($userToken, $channel, $messageId);
    }

    public function testIfAckWorks(): void
    {
        $store = new APCUStore();
        $userToken = 'peter@pass';
        $channel = 'peters_news';
        //autoack
        $messageId = $store->publish($channel, 'some message');
        self::assertEquals('some message', $store->getMessage($userToken, $channel, $messageId)['message']);
        self::assertCount(1, $store->getMessages($userToken, $channel));
        $store->ack($userToken, $channel, $messageId);
        self::assertEmpty($store->getMessages($userToken, $channel));
        $this->expectException(NotFoundException::class);
        $store->getMessage($userToken, $channel, $messageId);
    }
}
