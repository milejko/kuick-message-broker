<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Kuick\MessageBroker\Store;

use Kuick\MessageBroker\Infrastructure\MessageStore\FilesystemStore;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Disk backed publisher
 */
class FilesystemStoreTest extends \PHPUnit\Framework\TestCase
{
    public function testIfStandardFlowWorksCorrectly(): void
    {
        $dm = new FilesystemStore(new Filesystem());
        $userToken = 'john-doe@my_very_secret.1789';
        $channel = 'sample-channel';

        $messageId = $dm->publish($channel, 'sample message', 37);
        self::assertNotEmpty($messageId);

        self::assertEquals('sample message', $dm->getMessage($channel, $messageId, $userToken)['message']);
        self::assertEquals($messageId, $dm->getMessage($channel, $messageId, $userToken)['messageId']);
        self::assertEquals(37, $dm->getMessage($channel, $messageId, $userToken)['ttl']);
        self::assertArrayHasKey('createTime', $dm->getMessage($channel, $messageId, $userToken));

        $messages = $dm->getMessages($channel, $userToken);
        self::assertNotEmpty($messages);

        foreach ($messages as $message) {
            $message = $dm->getMessage($channel, $messageId, $userToken);
            self::assertEquals($messageId, $message['messageId']);
            self::assertEquals('sample message', $message['message']);
            self::assertEquals(37, $message['ttl']);
            self::assertArrayHasKey('createTime', $message);

            $dm->ack($channel, $messageId, $userToken);
        }
        self::assertEquals([], $dm->getMessages($channel, $userToken));
    }

    public function testIfAutoAckWorks(): void
    {
        $dm = new FilesystemStore(new Filesystem());
        $userToken = 'jane-doe@my_very_secret.1789';
        $channel = 'another-channel';

        $messageId = $dm->publish($channel, 'sample message', 60);
        self::assertNotEmpty($messageId);

        $messages = $dm->getMessages($channel, $userToken, true);
        self::assertNotEmpty($messages);
    }

    public function testIfOneSecondMessageLivesLessThan2Seconds(): void
    {
        $dm = new FilesystemStore(new Filesystem());
        $userToken = 'jack-doe@my_very_secret.1789';
        $channel = 'yet-another-channel';

        $messageId = $dm->publish($channel, 'sample message', 1);
        self::assertNotEmpty($messageId);

        $messages = $dm->getMessages($channel, $userToken, false);
        self::assertNotEmpty($messages);
        sleep(1);

        $messages = $dm->getMessages($channel, $userToken, false);
        self::assertNotEmpty($messages);
    }
}
