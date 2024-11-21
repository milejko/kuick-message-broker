<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Kuick\MessageBroker\Store;

use Kuick\MessageBroker\Infrastructure\FilesystemStore;

/**
 * Disk backed publisher
 */
class FilesystemStoreTest extends \PHPUnit\Framework\TestCase
{
    public function testIfStandardFlowWorksCorrectly(): void
    {
        $dm = new FilesystemStore();
        $userToken = 'john-doe@my_very_secret.1789';
        $channel = 'sample-channel';

        $messageId = $dm->publish($channel, 'sample message', 37);
        self::assertNotEmpty($messageId);

        self::assertEquals('sample message', $dm->getMessage($userToken, $channel, $messageId)['message']);
        self::assertEquals($messageId, $dm->getMessage($userToken, $channel, $messageId)['messageId']);
        self::assertEquals(37, $dm->getMessage($userToken, $channel, $messageId)['ttl']);
        self::assertArrayHasKey('createTime', $dm->getMessage($userToken, $channel, $messageId));

        $messages = $dm->getMessages($userToken, $channel);
        self::assertNotEmpty($messages);

        foreach ($messages as $message) {
            $message = $dm->getMessage($userToken, $channel, $messageId);
            self::assertEquals($messageId, $message['messageId']);
            self::assertEquals('sample message', $message['message']);
            self::assertEquals(37, $message['ttl']);
            self::assertArrayHasKey('createTime', $message);

            $dm->ack($userToken, $channel, $messageId);
        }
        self::assertEquals([], $dm->getMessages($userToken, $channel));
    }

    public function testIfAutoAckWorks(): void
    {
        $dm = new FilesystemStore();
        $userToken = 'jane-doe@my_very_secret.1789';
        $channel = 'another-channel';

        $messageId = $dm->publish($channel, 'sample message', 60);
        self::assertNotEmpty($messageId);

        $messages = $dm->getMessages($userToken, $channel, true);
        self::assertNotEmpty($messages);
    }

    public function testIfOneSecondMessageLivesLessThan2Seconds(): void
    {
        $dm = new FilesystemStore();
        $userToken = 'jack-doe@my_very_secret.1789';
        $channel = 'yet-another-channel';

        $messageId = $dm->publish($channel, 'sample message', 1);
        self::assertNotEmpty($messageId);

        $messages = $dm->getMessages($userToken, $channel, false);
        self::assertNotEmpty($messages);
        sleep(1);

        $messages = $dm->getMessages($userToken, $channel, false);
        self::assertNotEmpty($messages);
    }
}
