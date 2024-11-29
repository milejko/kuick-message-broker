<?php

/**
 * Kuick Message Broker
 *
 * @link       https://github.com/milejko/kuick-message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace KuickMessageBroker\Api\UI;

use Kuick\Http\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    private const DEFAULT_RESPONSE = [
        'application' => 'Message Broker',
        'api' => [
            'publish message' => 'POST:/api/message?channel=news&ttl=3600',
            'get message list' => 'GET:/api/messages?channel=news',
            'get message' => 'GET:/api/message?channel=news&messageId=some-id&autoack=0',
            'ack message' => 'POST:/api/message/ack?channel=news&messageId=some-id'
        ]
    ];

    public function __invoke(): JsonResponse
    {
        return new JsonResponse(self::DEFAULT_RESPONSE);
    }
}
