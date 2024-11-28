<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use KuickMessageBroker\Api\UI\HomeAction;
use Nyholm\Psr7\ServerRequest;

use function PHPUnit\Framework\assertEquals;

class HomeActionTest extends \PHPUnit\Framework\TestCase
{
    public function testStandardFlow(): void
    {
        $request = new ServerRequest('GET', 'whatever');
        $response = (new HomeAction())($request);
        assertEquals(200, $response->getStatusCode());
        assertEquals('{"application":"Message Broker","api":{"publish message":"POST:\/api\/message?channel=news&ttl=3600","get message list":"GET:\/api\/messages?channel=news","get message":"GET:\/api\/message?channel=news&messageId=some-id&autoack=0","ack message":"POST:\/api\/message\/ack?channel=news&messageId=some-id"}}', $response->getBody()->getContents());
    }
}
