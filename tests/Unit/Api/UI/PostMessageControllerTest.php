<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use KuickMessageBroker\Api\UI\PostMessageController;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;
use Tests\KuickMessageBroker\Mocks\InMemoryStorageAdapterMock;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

class PostMessageControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testStandardFlow(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $request = new ServerRequest('GET', 'whatever');
        $response = (new PostMessageController($store, new NullLogger()))('test', $request);
        assertEquals(201, $response->getStatusCode());
        assertArrayHasKey('messageId', json_decode($response->getBody()->getContents(), true));
    }
}
