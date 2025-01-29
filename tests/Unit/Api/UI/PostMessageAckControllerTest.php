<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use Kuick\Http\NotFoundException;
use KuickMessageBroker\Api\UI\PostMessageAckController;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;
use Tests\KuickMessageBroker\Mocks\InMemoryStorageAdapterMock;

class PostMessageAckControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testStandardFlow(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $messageId = $store->publish('test', 'msg');
        $request = new ServerRequest('POST', 'whatever?channel=test&messageId=' . $messageId);
        $response = (new PostMessageAckController($store, new NullLogger()))($request);
        $this->assertEquals(202, $response->getStatusCode());
        $this->assertArrayHasKey('messageId', json_decode($response->getBody()->getContents(), true));
    }

    public function testMessageNotFound(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $request = new ServerRequest('POST', 'whatever?channel=test&messageId=invalid-id');
        $response = (new PostMessageAckController($store, new NullLogger()))($request);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
