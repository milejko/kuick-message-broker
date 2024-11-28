<?php

namespace Tests\KuickMessageBroker\Unit\Infrastructure\MessageStore;

use KuickMessageBroker\Infrastructure\MessageStore\MessageNotFoundException;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Tests\KuickMessageBroker\Mocks\InMemoryStorageAdapterMock;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;

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
