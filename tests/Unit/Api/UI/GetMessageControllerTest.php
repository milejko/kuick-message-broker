<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use KuickMessageBroker\Api\UI\GetMessageController;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;
use Tests\KuickMessageBroker\Mocks\InMemoryStorageAdapterMock;

class GetMessageControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testMessageIsReceived(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $messageId = $store->publish('test', 'example');
        $request = new ServerRequest('GET', 'whatever?channel=test&messageId=' . $messageId);
        $response = (new GetMessageController($store, new NullLogger()))($request);
        $this->assertEquals(200, $response->getStatusCode());
        $responseArray = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('messageId', $responseArray);
        $this->assertArrayHasKey('message', $responseArray);
        $this->assertArrayHasKey('createTime', $responseArray);
        $this->assertArrayHasKey('ttl', $responseArray);
        $this->assertArrayHasKey('acked', $responseArray);
        $this->assertEquals(300, $responseArray['ttl']);
        $this->assertEquals('example', $responseArray['message']);
    }

    public function testMessageNotFound(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $request = new ServerRequest('GET', 'whatever?channel=test&messageId=inexistent-id');
        $response = (new GetMessageController($store, new NullLogger()))($request);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
