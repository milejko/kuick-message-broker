<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Kuick\MessageBroker\Unit\Infrastructure\MessageStore;

use Kuick\MessageBroker\Infrastructure\MessageStore\MessageNotFoundException;
use Kuick\MessageBroker\Infrastructure\MessageStore\MessageStore;
use Tests\Kuick\MessageBroker\Mocks\InMemoryStorageAdapterMock;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

class MessageStoreTest extends \PHPUnit\Framework\TestCase
{
    public function testStandardFlow(): void
    {
        $ms = new MessageStore(new InMemoryStorageAdapterMock());
        $channel = 'my-channel';
        $user = 'user@pass';
        assertEquals([], $ms->getMessages($channel, $user));
        $messageId = $ms->publish($channel, 'my-message');
        assertNotEmpty($messageId);
        assertCount(1, $ms->getMessages($channel, $user));
        assertEquals('my-message', $ms->getMessage($channel, $messageId, $user)['message']);
        assertEquals(300, $ms->getMessage($channel, $messageId, $user)['ttl']);
        //same message with auto ack
        assertEquals('my-message', $ms->getMessage($channel, $messageId, $user, true)['message']);
        //now message should not be found
        $this->expectException(MessageNotFoundException::class);
        $ms->getMessage($channel, $messageId, $user);
        //assertNull($ms->getMessage($channel, $messageId, $user));
    }
}
