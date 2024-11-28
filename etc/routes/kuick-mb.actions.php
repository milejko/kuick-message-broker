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
use KuickMessageBroker\Api\UI\GetMessageAction;
use KuickMessageBroker\Api\UI\GetMessagesAction;
use KuickMessageBroker\Api\UI\HomeAction;
use KuickMessageBroker\Api\UI\PostMessageAckAction;
use KuickMessageBroker\Api\UI\PostMessageAction;

return [
    [
        'pattern' => '/',
        'action' => HomeAction::class
    ],
    [
        'pattern' => '/api/message',
        'action' => GetMessageAction::class,
        'guards' => [TokenGuard::class, MessageIdParamGuard::class]
    ],
    [
        'pattern' => '/api/messages',
        'action' => GetMessagesAction::class,
        'guards' => [TokenGuard::class],
    ],
    [
        'method' => 'POST',
        'pattern' => '/api/message',
        'action' => PostMessageAction::class,
        'guards' => [TokenGuard::class],
    ],
    [
        'method' => 'POST',
        'pattern' => '/api/message/ack',
        'action' => PostMessageAckAction::class,
        'guards' => [TokenGuard::class, MessageIdParamGuard::class],
    ],
];