<?php

/**
 * Kuick Message Broker (https://github.com/milejko/kuick-message-broker)
 *
 * @link       https://github.com/milejko/kuick-message-broker
 * @copyright  Copyright (c) 2010-2024 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

use Kuick\MessageBroker\Api\Security\MessageIdParamGuard;
use Kuick\MessageBroker\Api\Security\TokenGuard;
use Kuick\MessageBroker\Api\UI\GetMessageAction;
use Kuick\MessageBroker\Api\UI\GetMessagesAction;
use Kuick\MessageBroker\Api\UI\HomeAction;
use Kuick\MessageBroker\Api\UI\PostMessageAckAction;
use Kuick\MessageBroker\Api\UI\PostMessageAction;

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