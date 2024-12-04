<?php

namespace Tests\KuickMessageBroker\Unit\UI;

use KuickMessageBroker\Api\UI\HomeController;
use Nyholm\Psr7\ServerRequest;

use function PHPUnit\Framework\assertEquals;

class HomeControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testStandardFlow(): void
    {
        $response = (new HomeController())();
        assertEquals(200, $response->getStatusCode());
        assertEquals('{"application":"Message Broker","api":{"publish message":"POST:\/api\/message\/channel-name?ttl=3600","get message list":"GET:\/api\/messages\/channel-name","get message":"GET:\/api\/message\/channel-name\/message-id?autoack=0","ack message":"POST:\/api\/message\/ack\/channel-name\/message-id"}}', $response->getBody()->getContents());
    }
}
