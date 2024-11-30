<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use Kuick\Http\NotFoundException;
use KuickMessageBroker\Api\UI\GetMessageController;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;
use Tests\KuickMessageBroker\Mocks\InMemoryStorageAdapterMock;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

class GetMessageControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testMessageIsReceived(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $messageId = $store->publish('test', 'example');
        $request = new ServerRequest('GET', 'whatever');
        $response = (new GetMessageController($store, new NullLogger()))('test', $messageId, $request);
        assertEquals(200, $response->getStatusCode());
        $responseArray = json_decode($response->getBody()->getContents(), true);
        assertArrayHasKey('messageId', $responseArray);
        assertArrayHasKey('message', $responseArray);
        assertArrayHasKey('createTime', $responseArray);
        assertArrayHasKey('ttl', $responseArray);
        assertArrayHasKey('acked', $responseArray);
        assertEquals(300, $responseArray['ttl']);
        assertEquals('example', $responseArray['message']);
    }

    public function testMessageNotFound(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $request = new ServerRequest('GET', 'whatever');
        $this->expectException(NotFoundException::class);
        (new GetMessageController($store, new NullLogger()))('test', 'inexistent-id', $request);
    }
}
