<?php

/**
 * Message Broker
 *
 * @link       https://github.com/milejko/message-broker.git
 * @copyright  Copyright (c) 2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace MessageBroker\Server\Actions;

use MessageBroker\Server\Action;
use MessageBroker\Server\JsonResponse;
use MessageBroker\Server\Request;

class RootAction implements Action
{
    private const DEFAULT_RESPONSE = [
        'application' => 'Message Broker',
        'api' => [
            'publish message' => 'POST:/api/message?channel=news&ttl=3600',
            'get message list' => 'GET:/api/messages?channel=news',
            'get message' => 'GET:/api/message?channel=news&messageId=some-id',
            'ack message' => 'PUT:/api/message/ack?channel=news&messageId=some-id'
        ]
    ];

    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse(self::DEFAULT_RESPONSE);
    }
}