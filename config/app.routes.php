<?php

/**
 * Kuick Framework (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-framework
 * @copyright  Copyright (c) 2010-2025 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://github.com/milejko/kuick-message-broker?tab=MIT-1-ov-file#readme New BSD License
 */

use Kuick\Framework\Config\RouteConfig;
use Kuick\Http\Message\JsonResponse;
use KuickMessageBroker\Api\UI\GetMessageController;
use KuickMessageBroker\Api\UI\GetMessagesController;
use KuickMessageBroker\Api\UI\PostMessageAckController;
use KuickMessageBroker\Api\UI\PostMessageController;

// routing configuration
return [
    // Inline home route
    new RouteConfig(
        '/',
        function (): JsonResponse {
            return new JsonResponse(['message' => 'Kuick Message Broker says hello!']);
        },
    ),
    new RouteConfig(
        '/api/message/(?<channel>[a-zA-Z0-9]+)/(?<messageId>[a-z0-9]{32})',
        GetMessageController::class,
    ),
    new RouteConfig(
        '/api/messages/(?<channel>[a-zA-Z0-9-_]{2,64})',
        GetMessagesController::class,
    ),
    new RouteConfig(
        '/api/message/(?<channel>[a-zA-Z0-9-_]{2,64})',
        PostMessageController::class,
        ['method' => 'POST'],
    ),
    new RouteConfig(
        '/api/message/ack/(?<channel>[a-zA-Z0-9-_]+)/(?<messageId>[a-z0-9]{32})',
        PostMessageAckController::class,
        ['method' => 'POST'],
    ),
];
