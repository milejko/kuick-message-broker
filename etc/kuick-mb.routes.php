<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use KuickMessageBroker\Api\Security\MessageIdParamGuard;
use KuickMessageBroker\Api\Security\TokenGuard;
use KuickMessageBroker\Api\UI\GetMessageController;
use KuickMessageBroker\Api\UI\GetMessagesController;
use KuickMessageBroker\Api\UI\HomeController;
use KuickMessageBroker\Api\UI\PostMessageAckController;
use KuickMessageBroker\Api\UI\PostMessageController;

return [
    [
        'path' => '/',
        'controller' => HomeController::class
    ],
    [
        'path' => '/api/message/(?<channel>[a-zA-Z0-9]+)/(?<messageId>[a-z0-9]{32})',
        'controller' => GetMessageController::class,
        'guards' => [TokenGuard::class]
    ],
    [
        'path' => '/api/messages/(?<channel>[a-zA-Z0-9-_]{2,64})',
        'controller' => GetMessagesController::class,
        'guards' => [TokenGuard::class],
    ],
    [
        'method' => 'POST',
        'path' => '/api/message/(?<channel>[a-zA-Z0-9-_]{2,64})',
        'controller' => PostMessageController::class,
        'guards' => [TokenGuard::class],
    ],
    [
        'method' => 'POST',
        'path' => '/api/message/ack/(?<channel>[a-zA-Z0-9-_]+)/(?<messageId>[a-z0-9]{32})',
        'controller' => PostMessageAckController::class,
        'guards' => [TokenGuard::class],
    ],
];