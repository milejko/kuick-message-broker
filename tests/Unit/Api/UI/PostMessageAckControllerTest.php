<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use Kuick\Http\NotFoundException;
use KuickMessageBroker\Api\UI\PostMessageAckController;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;
use Tests\KuickMessageBroker\Mocks\InMemoryStorageAdapterMock;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

class PostMessageAckControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testStandardFlow(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $messageId = $store->publish('test', 'msg');
        $request = new ServerRequest('POST', 'whatever');
        $response = (new PostMessageAckController($store, new NullLogger()))('test', $messageId, $request);
        assertEquals(202, $response->getStatusCode());
        assertArrayHasKey('messageId', json_decode($response->getBody()->getContents(), true));
    }

    public function testMessageNotFound(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $request = new ServerRequest('POST', 'whatever');
        $this->expectException(NotFoundException::class);
        (new PostMessageAckController($store, new NullLogger()))('test', 'invalid-id', $request);
    }
}
