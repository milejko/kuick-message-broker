<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use KuickMessageBroker\Api\UI\GetMessagesController;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;
use Tests\KuickMessageBroker\Mocks\InMemoryStorageAdapterMock;

use function PHPUnit\Framework\assertEquals;

class GetMessagesControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testStandardFlow(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $request = new ServerRequest('GET', 'whatever');
        $response = (new GetMessagesController($store, new NullLogger()))('test', $request);
        assertEquals(200, $response->getStatusCode());
        assertEquals('[]', $response->getBody()->getContents());
    }
}
