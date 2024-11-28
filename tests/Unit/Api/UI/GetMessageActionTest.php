<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use Kuick\Http\NotFoundException as HttpNotFoundException;
use KuickMessageBroker\Api\UI\GetMessagesAction;
use KuickMessageBroker\Infrastructure\MessageStore\MessageStore;
use Nyholm\Psr7\ServerRequest;
use Psr\Log\NullLogger;
use Tests\KuickMessageBroker\Mocks\InMemoryStorageAdapterMock;

use function PHPUnit\Framework\assertEquals;

class GetMessageAction extends \PHPUnit\Framework\TestCase
{
    public function testStandardFlow(): void
    {
        $store = new MessageStore(new InMemoryStorageAdapterMock());
        $request = new ServerRequest('GET', 'whatever?channel=test');
        $response = (new GetMessagesAction($store, new NullLogger))($request);
        assertEquals(200, $response->getStatusCode());
        assertEquals('[]', $response->getBody()->getContents());
    }
}